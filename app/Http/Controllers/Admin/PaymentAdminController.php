<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\PointsTransaction;
use App\Services\ActivityLogService;
use Illuminate\Http\Request;

class PaymentAdminController extends Controller
{
    public function __construct(protected ActivityLogService $activityLogService) {}

    public function index(Request $request)
    {
        $query = Payment::with('user');

        // Search filter (User name/email, sender phone, transaction ID)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('transaction_id', 'like', "%{$search}%")
                  ->orWhere('sender_phone', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($uq) use ($search) {
                      $uq->where('name', 'like', "%{$search}%")
                         ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        // Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Payment method filter
        if ($request->filled('method')) {
            $query->where('payment_method', $request->method);
        }

        $payments = $query->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.payments.index', compact('payments'));
    }

    public function approve(Payment $payment)
    {
        if ($payment->status !== 'pending') {
            return back()->with('error', __('Only pending payments can be approved.'));
        }

        // Prevent double credit
        if ($payment->transaction_id && Payment::where('transaction_id', $payment->transaction_id)->where('status', 'success')->exists()) {
            return back()->with('error', __('This transaction ID has already been verified and credited.'));
        }

        $user = $payment->user;
        
        // Update payment record
        $payment->update([
            'status' => 'success',
        ]);

        // Credit user points
        $user->increment('points', $payment->points);

        // Log points transaction
        PointsTransaction::create([
            'user_id'        => $user->id,
            'type'           => 'purchase',
            'amount'         => $payment->points,
            'balance_after'  => $user->points,
            'description'    => "Points purchase approved by admin (+{$payment->points} pts)",
            'reference_type' => Payment::class,
            'reference_id'   => $payment->id,
        ]);

        $this->activityLogService->log(
            'approve_payment',
            "Approved payment of {$payment->amount_usd} USD ({$payment->points} pts) for user: {$user->email}",
            'payment',
            $payment->id
        );

        return back()->with('success', __('Payment approved and points credited successfully.'));
    }

    public function reject(Payment $payment)
    {
        if ($payment->status !== 'pending') {
            return back()->with('error', __('Only pending payments can be rejected.'));
        }

        // Update payment record to failed
        $payment->update([
            'status' => 'failed',
        ]);

        $this->activityLogService->log(
            'reject_payment',
            "Rejected payment of {$payment->amount_usd} USD for user: {$payment->user->email}",
            'payment',
            $payment->id
        );

        return back()->with('success', __('Payment rejected successfully.'));
    }
}
