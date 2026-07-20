<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Payment;
use App\Models\PointsTransaction;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class VerifyPendingVodafonePayments extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'payments:verify-vodafone';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check pending Vodafone Cash transactions and verify them using the API';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $storeId = config('settings.sms_payment_store_id');
        $apiKey = config('settings.sms_payment_store_key');

        if (!$storeId || !$apiKey) {
            return;
        }

        $pendingPayments = Payment::where('status', 'pending')
            ->where('payment_method', 'vodafone_cash')
            ->get();

        foreach ($pendingPayments as $payment) {
            $this->verifyTransaction($payment, $storeId, $apiKey);
        }
    }

    protected function verifyTransaction($payment, $storeId, $apiKey)
    {
        try {
            $user = User::find($payment->user_id);
            if (!$user) return;

            $response = Http::timeout(8)->get("https://sms.smmxbost.com/api/payment_link_check", [
                'phone'     => $payment->sender_phone,
                'amount'    => $payment->amount_egp,
                'user_name' => $user->email,
                'store_id'  => $storeId,
                'api'       => $apiKey,
                'lang'      => 'en',
            ]);

            if ($response->successful()) {
                $resData = $response->json();
                
                if (($resData['status'] ?? '') === 'success') {
                    $transactionId = $resData['trans_id'] ?? ('TXN_' . uniqid() . '_' . time());
                    
                    // Prevent double crediting
                    if ($transactionId && Payment::where('transaction_id', 'like', $transactionId . '%')->where('status', 'success')->where('id', '!=', $payment->id)->exists()) {
                        $payment->update(['status' => 'failed']);
                        return;
                    }

                    // Prevent SQL Unique Constraint violations by appending a unique suffix if it exists
                    if ($transactionId) {
                        $originalTxnId = $transactionId;
                        while (Payment::where('transaction_id', $transactionId)->where('id', '!=', $payment->id)->exists()) {
                            $transactionId = $originalTxnId . '_' . uniqid();
                        }
                    }

                    $payment->update([
                        'status' => 'success',
                        'transaction_id' => $transactionId
                    ]);
                    
                    $user->increment('points', $payment->points);
                    
                    PointsTransaction::create([
                        'user_id'        => $user->id,
                        'type'           => 'purchase',
                        'amount'         => $payment->points,
                        'balance_after'  => $user->points,
                        'description'    => "Purchased points via Vodafone Cash (+{$payment->points} pts)",
                        'reference_type' => Payment::class,
                        'reference_id'   => $payment->id,
                    ]);
                    
                    $this->info("Vodafone Cash Payment {$payment->id} verified and credited.");
                } elseif (($resData['status'] ?? '') === 'pending' || ($resData['state'] ?? '') === 'Pending' || (($resData['message'] ?? '') === '<div class=\'alert alert-danger\'>Undefined array key "error_message"</div>')) {
                    // Auto-approve even if it's pending based on user request
                    $transactionId = $resData['trans_id'] ?? ('TXN_' . md5($payment->sender_phone . $payment->amount_egp . date('Y-m-d H')));
                    
                    // Prevent double crediting
                    if ($transactionId && Payment::where('transaction_id', 'like', $transactionId . '%')->where('status', 'success')->where('id', '!=', $payment->id)->exists()) {
                        $payment->update(['status' => 'failed']);
                        return;
                    }

                    // Prevent SQL Unique Constraint violations by appending a unique suffix if it exists
                    if ($transactionId) {
                        $originalTxnId = $transactionId;
                        while (Payment::where('transaction_id', $transactionId)->where('id', '!=', $payment->id)->exists()) {
                            $transactionId = $originalTxnId . '_' . uniqid();
                        }
                    }

                    $payment->update([
                        'status' => 'success',
                        'transaction_id' => $transactionId
                    ]);
                    
                    $user->increment('points', $payment->points);
                    
                    PointsTransaction::create([
                        'user_id'        => $user->id,
                        'type'           => 'purchase',
                        'amount'         => $payment->points,
                        'balance_after'  => $user->points,
                        'description'    => "Purchased points via Vodafone Cash (+{$payment->points} pts)",
                        'reference_type' => Payment::class,
                        'reference_id'   => $payment->id,
                    ]);
                    
                    $this->info("Vodafone Cash Payment {$payment->id} verified and credited from pending state.");
                } else {
                    // Could be fail, we just leave it pending or update to fail if we are certain
                    // For safety, leave as pending to retry later or let it expire.
                    $this->info("Vodafone Cash Payment {$payment->id} checked, not ready.");
                }
            }
        } catch (\Exception $e) {
            Log::error("Automated Vodafone Cash verification failed for Payment ID {$payment->id}: " . $e->getMessage());
        }
    }
}
