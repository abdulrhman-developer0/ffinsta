<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\PointsTransaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    /**
     * Show available points calculator and options.
     */
    public function index()
    {
        $storeId = config('settings.sms_payment_store_id');
        $pointsPerUsd = intval(config('settings.points_per_usd', 200));
        $binancePayId = config('settings.binance_pay_id');
        $binanceQrCode = config('settings.binance_qr_code');

        $paymentInfo = null;
        $wallets = [];
        $rate = 50.00; // default fallback rate

        if ($storeId) {
            $apiKey = config('settings.sms_payment_store_key');
            $paymentInfo = Cache::remember('sms_payment_info_' . $storeId, 300, function () use ($storeId, $apiKey) {
                try {
                    $response = Http::timeout(5)->get("https://sms.5brahost.com/api/getPaymentInfo", [
                        'store_id' => $storeId,
                        'api'      => $apiKey,
                    ]);
                    if ($response->successful()) {
                        return $response->json();
                    }
                } catch (\Throwable $e) {
                    Log::error("Failed to fetch payment info from SMS gateway: " . $e->getMessage());
                }
                return null;
            });
        }

        if ($paymentInfo) {
            $rate = floatval($paymentInfo['rate'] ?? 50.00);
            $numbersString = $paymentInfo['number'] ?? '';
            $wallets = array_filter(array_map('trim', explode(',', $numbersString)));
        }

        return view('user.purchase.index', compact('wallets', 'rate', 'storeId', 'pointsPerUsd', 'binancePayId', 'binanceQrCode'));
    }

    /**
     * Verify payment on SMS Gateway or Binance Pay.
     */
    public function initiate(Request $request)
    {
        $request->validate([
            'usd_amount'     => ['required', 'numeric', 'min:1'],
            'payment_method' => ['required', 'in:vodafone_cash,binance_pay'],
            'sender_phone'   => ['required_if:payment_method,vodafone_cash', 'nullable', 'string', 'regex:/^01[0-2,5]\d{8}$/'],
            'transaction_id' => ['required_if:payment_method,binance_pay', 'nullable', 'string', 'max:100'],
        ]);

        $usdAmount = floatval($request->usd_amount);
        $pointsPerUsd = intval(config('settings.points_per_usd', 200));
        $pointsToCredit = (int) round($usdAmount * $pointsPerUsd);
        $user = auth()->user();

        if ($request->payment_method === 'vodafone_cash') {
            $storeId = config('settings.sms_payment_store_id');
            $apiKey = config('settings.sms_payment_store_key');
            if (!$storeId || !$apiKey) {
                return $request->wantsJson() ? response()->json(['success' => false, 'message' => __('Payment gateway not configured.')]) : back()->with('error', __('Payment gateway not configured.'));
            }

            // Get fresh exchange rate from SMS Gateway
            $rate = 50.00;
            try {
                $response = Http::timeout(5)->get("https://sms.5brahost.com/api/getPaymentInfo", [
                    'store_id' => $storeId,
                    'api'      => $apiKey,
                ]);
                if ($response->successful()) {
                    $rate = floatval($response->json()['rate'] ?? 50.00);
                }
            } catch (\Throwable $e) {
                Log::warning("Could not fetch fresh rate, falling back to 50: " . $e->getMessage());
            }

            $amountEgp = round($usdAmount * $rate, 2);

            // Check transaction status on the SMS Gateway
            $gatewayVerified = false;
            $gatewayPending = false;
            $transactionId = null;

            try {
                $response = Http::timeout(8)->get("https://sms.5brahost.com/api/payment_link_check", [
                    'phone'     => $request->sender_phone,
                    'amount'    => $amountEgp,
                    'user_name' => $user->email,
                    'store_id'  => $storeId,
                    'api'       => $apiKey,
                    'lang'      => app()->getLocale() === 'ar' ? 'ar' : 'en',
                ]);

                if ($response->successful()) {
                    $resData = $response->json();
                    Log::info("SMS Gateway Response:", $resData);
                    
                    if (($resData['status'] ?? '') === 'success') {
                        $gatewayVerified = true;
                        $transactionId = $resData['trans_id'] ?? ('TXN_' . md5($request->sender_phone . $amountEgp . date('Y-m-d H')));
                    } elseif (($resData['status'] ?? '') === 'pending' || ($resData['state'] ?? '') === 'Pending') {
                        $gatewayVerified = true; // Auto-approve pending
                        $transactionId = $resData['trans_id'] ?? ('TXN_' . md5($request->sender_phone . $amountEgp . date('Y-m-d H')));
                    } elseif (($resData['message'] ?? '') === '<div class=\'alert alert-danger\'>Undefined array key "error_message"</div>') {
                        // This specific error on 5brahost means the transaction is pending
                        $gatewayVerified = true; // Auto-approve pending
                        $transactionId = 'TXN_' . md5($request->sender_phone . $amountEgp . date('Y-m-d H'));
                    }
                } else {
                    Log::error("SMS Gateway API returned HTTP " . $response->status());
                }
            } catch (\Throwable $e) {
                Log::error("SMS Payment verification connection error: " . $e->getMessage());
                return $request->wantsJson() ? response()->json(['success' => false, 'message' => __('Gateway verification timeout. Please try again.')]) : back()->with('error', __('Gateway verification timeout. Please try again.'));
            }

            if ($gatewayVerified) {
                // If the transaction has already been credited (e.g. by the callback), redirect with a success message instead of showing an error alert
                if ($transactionId && Payment::where('transaction_id', 'like', $transactionId . '%')->where('status', 'success')->exists()) {
                    return $request->wantsJson() ? response()->json(['success' => true, 'message' => __('Your payment has already been verified and points have been credited successfully!'), 'redirect' => route('user.dashboard')]) : redirect()->route('user.dashboard')->with('success', __('Your payment has already been verified and points have been credited successfully!'));
                }

                // Prevent SQL Unique Constraint violations by appending a unique suffix if the transaction ID already exists
                if ($transactionId) {
                    $originalTxnId = $transactionId;
                    while (Payment::where('transaction_id', $transactionId)->exists()) {
                        $transactionId = $originalTxnId . '_' . uniqid();
                    }
                }

                $payment = Payment::create([
                    'user_id'        => $user->id,
                    'amount_egp'     => $amountEgp,
                    'amount_usd'     => $usdAmount,
                    'points'         => $pointsToCredit,
                    'payment_method' => 'vodafone_cash',
                    'sender_phone'   => $request->sender_phone,
                    'transaction_id' => $transactionId,
                    'status'         => 'success',
                ]);

                $user->increment('points', $pointsToCredit);

                PointsTransaction::create([
                    'user_id'        => $user->id,
                    'type'           => 'purchase',
                    'amount'         => $pointsToCredit,
                    'balance_after'  => $user->points,
                    'description'    => "Purchased points (+{$pointsToCredit} pts)",
                    'reference_type' => Payment::class,
                    'reference_id'   => $payment->id,
                ]);

                return $request->wantsJson() ? response()->json(['success' => true, 'message' => __(':points points credited successfully!', ['points' => $pointsToCredit]), 'redirect' => route('user.dashboard')]) : redirect()->route('user.dashboard')->with('success', __(':points points credited successfully!', ['points' => $pointsToCredit]));
            }

            // Save pending log (or success if verified)
            $payment = Payment::where('transaction_id', $transactionId)->latest()->first();

            if (!$payment) {
                $payment = Payment::create([
                    'user_id'        => $user->id,
                    'amount_egp'     => $amountEgp,
                    'amount_usd'     => $usdAmount,
                    'points'         => $pointsToCredit,
                    'payment_method' => 'vodafone_cash',
                    'sender_phone'   => $request->sender_phone ?? '',
                    'status'         => $gatewayVerified ? 'success' : 'pending',
                    'transaction_id' => $transactionId,
                ]);
            } else {
                $payment->update(['status' => $gatewayVerified ? 'success' : 'pending']);
            }

            if ($gatewayVerified) {
                $user->increment('points', $pointsToCredit);
                
                \App\Models\PointsTransaction::create([
                    'user_id'        => $user->id,
                    'type'           => 'purchase',
                    'amount'         => $pointsToCredit,
                    'balance_after'  => $user->points,
                    'description'    => "Purchased points via Vodafone Cash (+{$pointsToCredit} pts)",
                    'reference_type' => Payment::class,
                    'reference_id'   => $payment->id,
                ]);

                return $request->wantsJson() ? response()->json(['success' => true, 'message' => __('Points purchased successfully!'), 'redirect' => route('user.dashboard')]) : redirect()->route('user.purchase.index')->with('success', __('Points purchased successfully!'));
            }

            if ($gatewayPending) {
                return $request->wantsJson() ? response()->json(['success' => true, 'message' => __('Your transfer is currently pending approval on the gateway. Please check your balance shortly.')]) : back()->with('success', __('Your transfer is currently pending approval on the gateway. Please check your balance shortly.'));
            }

            return $request->wantsJson() ? response()->json(['success' => false, 'message' => __('Transfer not verified. If you recently made the transfer, please wait 1 minute and verify again.')]) : back()->with('error', __('Transfer not verified. If you recently made the transfer, please wait 1 minute and verify again.'));

        } elseif ($request->payment_method === 'binance_pay') {
            $orderId = trim($request->transaction_id);

            $payment = Payment::where('transaction_id', $orderId)->first();

            // 1 & 2. Check if the Order ID already exists in the database
            if ($payment) {
                if ($payment->status === 'success') {
                    return $request->wantsJson() ? response()->json(['success' => false, 'message' => __('This transaction ID has already been used.')]) : back()->with('error', __('This transaction ID has already been used.'));
                }
                
                // If pending, try verifying immediately via API
                $verificationResult = $this->verifyBinanceTransaction($orderId, $usdAmount);
                if ($verificationResult === 'success') {
                    $this->creditBinancePayment($payment, $user, $usdAmount, $pointsToCredit);
                    return $request->wantsJson() ? response()->json(['success' => true, 'message' => __(':points points credited successfully via Binance!', ['points' => $pointsToCredit]), 'redirect' => route('user.dashboard')]) : redirect()->route('user.dashboard')->with('success', __(':points points credited successfully via Binance!', ['points' => $pointsToCredit]));
                }

                return $request->wantsJson() ? response()->json(['success' => true, 'message' => __('Your transaction is already being processed. Please wait for verification.')]) : back()->with('success', __('Your transaction is already being processed. Please wait for verification.'));
            }

            // 3. Order ID does not exist in the database
            $apiKey = config('settings.binance_api_key');
            $apiSecret = config('settings.binance_api_secret');

            // If API keys are not set, save as pending for manual verification
            if (!$apiKey || !$apiSecret) {
                Payment::create([
                    'user_id'        => $user->id,
                    'amount_egp'     => 0,
                    'amount_usd'     => $usdAmount,
                    'points'         => $pointsToCredit,
                    'payment_method' => 'binance_pay',
                    'transaction_id' => $orderId,
                    'status'         => 'pending',
                ]);
                return redirect()->route('user.dashboard')->with('success', __('Your Binance transfer has been submitted and is pending administrator review.'));
            }

            // Validate using Binance API
            $verificationResult = $this->verifyBinanceTransaction($orderId, $usdAmount);

            if ($verificationResult === 'invalid_amount') {
                return back()->with('error', __('Invalid transaction amount. Please check and try again.'));
            } elseif ($verificationResult === 'not_found') {
                return back()->with('error', __('Invalid transaction ID. Please check and try again.'));
            }

            // If valid (success), save to database with status Under Approval (pending) and verify
            $payment = Payment::create([
                'user_id'        => $user->id,
                'amount_egp'     => 0,
                'amount_usd'     => $usdAmount,
                'points'         => $pointsToCredit,
                'payment_method' => 'binance_pay',
                'transaction_id' => $orderId,
                'status'         => 'pending',
            ]);

            // Since it was 'success' in the API right now, we can update it immediately
            $this->creditBinancePayment($payment, $user, $usdAmount, $pointsToCredit);
            
            return redirect()->route('user.dashboard')->with('success', __(':points points credited successfully via Binance!', ['points' => $pointsToCredit]));
        }
    }

    /**
     * Webhook callback for asynchronous payment notifications.
     */
    public function callback(Request $request)
    {
        $secretKey = config('settings.sms_payment_store_key');
        
        Log::info("Payment callback received with payload: ", $request->all());
        
        if (!$secretKey || $request->input('key') !== $secretKey) {
            Log::warning("Unauthorized payment callback hit: mismatching key. Expected: {$secretKey}, Got: " . $request->input('key'));
            return response()->json(['status' => 'error', 'message' => 'Unauthorized'], 403);
        }

        if ($request->input('action') === 'addPayment') {
            $username = $request->input('username'); // User email
            $amountUsd = floatval($request->input('amount'));
            $senderPhone = $request->input('from');
            $transId = $request->input('trans_id');

            Log::info("Payment callback received for user: {$username}, amount: {$amountUsd} USD, trans_id: {$transId}");

            $user = User::where('email', $username)->first();
            if (!$user) {
                Log::error("User not found for payment callback: {$username}");
                return response()->json(['status' => 'error', 'message' => 'User not found'], 404);
            }

            // Check if transaction ID has already been credited (even with a suffix)
            if (Payment::where('transaction_id', 'like', $transId . '%')->where('status', 'success')->exists()) {
                Log::info("Payment transaction ID {$transId} already credited.");
                return response()->json(['status' => 'success', 'message' => 'Already credited']);
            }

            // Find matching pending payment by checking normalized phone numbers in PHP
            $payment = Payment::where('user_id', $user->id)
                ->where('payment_method', 'vodafone_cash')
                ->where('status', 'pending')
                ->get()
                ->first(function ($p) use ($senderPhone) {
                    $dbPhone = preg_replace('/\D/', '', $p->sender_phone);
                    $cbPhone = preg_replace('/\D/', '', $senderPhone);
                    
                    // Normalize both (remove leading 20, 0020, +, etc.)
                    if (str_starts_with($dbPhone, '20') && strlen($dbPhone) > 2) $dbPhone = '0' . substr($dbPhone, 2);
                    if (str_starts_with($cbPhone, '20') && strlen($cbPhone) > 2) $cbPhone = '0' . substr($cbPhone, 2);
                    if (str_starts_with($dbPhone, '0020') && strlen($dbPhone) > 4) $dbPhone = '0' . substr($dbPhone, 4);
                    if (str_starts_with($cbPhone, '0020') && strlen($cbPhone) > 4) $cbPhone = '0' . substr($cbPhone, 4);
                    
                    return $dbPhone === $cbPhone;
                });

            // Always calculate exact points based on ACTUAL transferred amount to prevent exploits
            $pointsPerUsd = intval(config('settings.points_per_usd', 200));
            $rate = 50.00;
            try {
                $storeId = config('settings.sms_payment_store_id');
                $apiKey = config('settings.sms_payment_store_key');
                $response = Http::timeout(5)->get("https://sms.5brahost.com/api/getPaymentInfo", [
                    'store_id' => $storeId,
                    'api'      => $apiKey,
                ]);
                if ($response->successful()) {
                    $rate = floatval($response->json()['rate'] ?? 50.00);
                }
            } catch (\Throwable $e) {
                Log::warning("Could not fetch fresh rate in callback, falling back to 50: " . $e->getMessage());
            }

            $amountEgp = round($amountUsd * $rate, 2);
            $pointsToCredit = 0;

            if ($payment) {
                // Determine if the callback USD amount matches the pending payment's USD amount (with small tolerance)
                if (abs($amountUsd - $payment->amount_usd) < 0.1) {
                    $pointsToCredit = $payment->points;
                }
                // Determine if the callback USD amount matches the pending payment's EGP amount (in case EGP was sent instead)
                elseif (abs($amountUsd - $payment->amount_egp) < 1.0) {
                    $pointsToCredit = $payment->points;
                    $amountUsd = $payment->amount_usd; // Keep USD correct
                }
                else {
                    // Fallback: calculate dynamically
                    $pointsToCredit = (int) round($amountUsd * $pointsPerUsd);
                }
            } else {
                // No pending payment found, calculate dynamically
                $pointsToCredit = (int) round($amountUsd * $pointsPerUsd);
            }

            if ($payment) {
                // Prevent duplicate transaction_id on update
                if ($transId) {
                    $originalTransId = $transId;
                    while (Payment::where('transaction_id', $transId)->where('id', '!=', $payment->id)->exists()) {
                        $transId = $originalTransId . '_' . uniqid();
                    }
                }

                // Update the pending payment with the ACTUAL amount received
                $payment->update([
                    'amount_egp'     => $amountEgp,
                    'amount_usd'     => $amountUsd,
                    'points'         => $pointsToCredit,
                    'status'         => 'success',
                    'transaction_id' => $transId,
                ]);
            } else {
                // Prevent duplicate transaction_id on create
                if ($transId) {
                    $originalTransId = $transId;
                    while (Payment::where('transaction_id', $transId)->exists()) {
                        $transId = $originalTransId . '_' . uniqid();
                    }
                }

                $payment = Payment::create([
                    'user_id'        => $user->id,
                    'amount_egp'     => $amountEgp,
                    'amount_usd'     => $amountUsd,
                    'points'         => $pointsToCredit,
                    'payment_method' => 'vodafone_cash',
                    'sender_phone'   => $senderPhone,
                    'transaction_id' => $transId,
                    'status'         => 'success',
                ]);
            }

            // Credit points
            $user->increment('points', $pointsToCredit);

            // Log transaction
            PointsTransaction::create([
                'user_id'        => $user->id,
                'type'           => 'purchase',
                'amount'         => $pointsToCredit,
                'balance_after'  => $user->points,
                'description'    => "Purchased points (Callback): (+{$pointsToCredit} pts)",
                'reference_type' => Payment::class,
                'reference_id'   => $payment->id,
            ]);

            return response()->json(['status' => 'success']);
        }

        return response()->json(['status' => 'error', 'message' => 'Unknown action'], 400);
    }

    /**
     * Query Binance API history to check transaction status.
     */
    protected function verifyBinanceTransaction($orderId, $amountUsd)
    {
        $apiKey = config('settings.binance_api_key');
        $apiSecret = config('settings.binance_api_secret');

        if (!$apiKey || !$apiSecret) {
            return 'pending';
        }

        try {
            $timestamp = number_format(microtime(true) * 1000, 0, '.', '');
            $queryString = "timestamp=" . $timestamp;
            $signature = hash_hmac('sha256', $queryString, $apiSecret);

            // Calling the official Binance Pay Trade/Transactions API history endpoint
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

                        if (($txnId && strtolower($txnId) === strtolower($orderId)) || ($oId && strtolower($oId) === strtolower($orderId))) {
                            // Match transaction amount (allowing tiny floats tolerances)
                            if (abs($txnAmount - $amountUsd) < 0.05) {
                                return 'success';
                            } else {
                                return 'invalid_amount';
                            }
                        }
                    }
                }
            }
        } catch (\Throwable $e) {
            Log::error("Binance API verification exception: " . $e->getMessage());
        }

        return 'not_found';
    }

    /**
     * Helper to mark a payment as success and credit the user points.
     */
    protected function creditBinancePayment($payment, $user, $usdAmount, $pointsToCredit)
    {
        $payment->update([
            'status' => 'success',
            'amount_usd' => $usdAmount,
            'points' => $pointsToCredit,
        ]);

        $user->increment('points', $pointsToCredit);

        PointsTransaction::create([
            'user_id'        => $user->id,
            'type'           => 'purchase',
            'amount'         => $pointsToCredit,
            'balance_after'  => $user->points,
            'description'    => "Purchased points via Binance (+{$pointsToCredit} pts)",
            'reference_type' => Payment::class,
            'reference_id'   => $payment->id,
        ]);
    }
}
