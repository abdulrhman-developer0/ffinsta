<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Payment;
use App\Models\PointsTransaction;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class VerifyPendingBinancePayments extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'payments:verify-binance';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check pending Binance Pay transactions and verify them using the API';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $apiKey = config('settings.binance_api_key');
        $apiSecret = config('settings.binance_api_secret');

        if (!$apiKey || !$apiSecret) {
            return;
        }

        $pendingPayments = Payment::where('status', 'pending')
            ->where('payment_method', 'binance_pay')
            ->get();

        foreach ($pendingPayments as $payment) {
            $this->verifyTransaction($payment, $apiKey, $apiSecret);
        }
    }

    protected function verifyTransaction($payment, $apiKey, $apiSecret)
    {
        try {
            $timestamp = number_format(microtime(true) * 1000, 0, '.', '');
            $queryString = "timestamp=" . $timestamp;
            $signature = hash_hmac('sha256', $queryString, $apiSecret);

            $response = Http::withHeaders([
                'X-MBX-APIKEY' => $apiKey
            ])->timeout(8)->get("https://api.binance.com/sapi/v1/pay/transactions?" . $queryString . "&signature=" . $signature);

            if ($response->successful()) {
                $res = $response->json();
                if (($res['code'] ?? '') === '000000' && isset($res['data'])) {
                    foreach ($res['data'] as $txn) {
                        $txnId = $txn['transactionId'] ?? $txn['tranId'] ?? null;
                        $oId = $txn['orderId'] ?? null;
                        $txnAmount = floatval($txn['amount'] ?? 0);

                        if (($txnId && strtolower($txnId) === strtolower($payment->transaction_id)) || ($oId && strtolower($oId) === strtolower($payment->transaction_id))) {
                            if (abs($txnAmount - $payment->amount_usd) < 0.05) {
                                // Success!
                                $payment->update(['status' => 'success']);
                                
                                $user = User::find($payment->user_id);
                                if ($user) {
                                    $user->increment('points', $payment->points);
                                    
                                    PointsTransaction::create([
                                        'user_id'        => $user->id,
                                        'type'           => 'purchase',
                                        'amount'         => $payment->points,
                                        'balance_after'  => $user->points,
                                        'description'    => "Purchased points via Binance (+{$payment->points} pts)",
                                        'reference_type' => Payment::class,
                                        'reference_id'   => $payment->id,
                                    ]);
                                }
                                $this->info("Payment {$payment->id} verified and credited.");
                                return;
                            }
                        }
                    }
                }
            }
        } catch (\Exception $e) {
            Log::error("Automated Binance verification failed for Payment ID {$payment->id}: " . $e->getMessage());
        }
    }
}
