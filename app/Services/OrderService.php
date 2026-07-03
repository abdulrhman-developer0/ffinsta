<?php

namespace App\Services;

use App\Exceptions\InsufficientPointsException;
use App\Models\Order;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OrderService
{
    public function __construct(
        protected PointsService       $pointsService,
        protected NotificationService $notificationService,
        protected SettingService      $settingService,
        protected TaskService         $taskService
    ) {}

    /**
     * Create a new order from a user request. Deducts points.
     *
     * @throws \Exception
     */
    public function create(
        int    $userId,
        ?int   $instagramAccountId,
        string $instagramUsername,
        int    $qty
    ): Order {
        return DB::transaction(function () use ($userId, $instagramAccountId, $instagramUsername, $qty) {
            $pointsPerFollower = (int) $this->settingService->get('points_per_follow', 10);
            $minPoints         = (int) $this->settingService->get('min_points_to_order', 0);
            $totalCost         = $qty * $pointsPerFollower;

            // Check minimum points requirement
            $user = \App\Models\User::lockForUpdate()->findOrFail($userId);
            if ($minPoints > 0 && $user->points < $minPoints) {
                throw new \Exception(__('You need at least :min points to create an order.', ['min' => $minPoints]));
            }

            // Deduct points (throws InsufficientPointsException if not enough)
            $this->pointsService->deduct(
                $userId,
                $totalCost,
                'spend',
                __('Order for :qty followers on @:username', ['qty' => $qty, 'username' => $instagramUsername]),
            );

            $autoApprove = (bool) $this->settingService->get('auto_approve_orders', 0);
            $status = $autoApprove ? 'active' : 'pending';

            // Create order
            $order = Order::create([
                'order_number'         => $this->generateOrderNumber(),
                'user_id'              => $userId,
                'instagram_account_id' => $instagramAccountId,
                'instagram_username'   => $instagramUsername,
                'requested_qty'        => $qty,
                'delivered_qty'        => 0,
                'points_cost'          => $totalCost,
                'status'               => $status,
                'priority'             => 'normal',
            ]);

            if ($status === 'active') {
                // We no longer generate tasks upfront. They are created dynamically on claim.
            }

            return $order;
        });
    }

    /**
     * Create an order manually by an admin (no point deduction).
     */
    public function createManual(
        int    $userId,
        ?int   $instagramAccountId,
        string $instagramUsername,
        int    $qty,
        string $status = 'pending',
        string $priority = 'normal',
        string $adminNotes = null
    ): Order {
        $pointsPerFollower = (int) $this->settingService->get('points_per_follow', 10);

        $order = Order::create([
            'order_number'         => $this->generateOrderNumber(),
            'user_id'              => $userId,
            'instagram_account_id' => $instagramAccountId,
            'instagram_username'   => $instagramUsername,
            'requested_qty'        => $qty,
            'delivered_qty'        => 0,
            'points_cost'          => $qty * $pointsPerFollower,
            'status'               => $status,
            'priority'             => $priority,
            'admin_notes'          => $adminNotes,
        ]);

        // If creating as active, they are available for dynamic claim
        if ($status === 'active') {
            // No longer generating upfront tasks
        }

        return $order;
    }

    /**
     * Activate an order (admin action) — generates follow tasks.
     */
    public function activate(Order $order): void
    {
        if ($order->status !== 'pending') {
            throw new \Exception(__('Only pending orders can be activated.'));
        }

        $order->update(['status' => 'active']);
        $this->notificationService->orderActivated($order->user_id, $order->order_number);
    }

    /**
     * Cancel an order — optionally refund points.
     */
    public function cancel(Order $order, bool $refund = true): void
    {
        if (in_array($order->status, ['completed', 'cancelled'])) {
            throw new \Exception(__('This order cannot be cancelled.'));
        }

        $order->update(['status' => 'cancelled']);

        // Cancel outstanding tasks
        $order->followTasks()->whereIn('status', ['available', 'assigned'])->update([
            'status'           => 'failed',
            'assigned_user_id' => null,
        ]);

        // Refund undelivered points
        $refundedPoints = 0;
        if ($refund) {
            $pointsPerFollower   = (int) $this->settingService->get('points_per_follow', 10);
            $undelivered         = $order->requested_qty - $order->delivered_qty;
            $refundedPoints      = $undelivered * $pointsPerFollower;

            if ($refundedPoints > 0) {
                $this->pointsService->credit(
                    $order->user_id,
                    $refundedPoints,
                    'admin_adjustment',
                    __('Refund for cancelled order #:number', ['number' => $order->order_number]),
                    'order',
                    $order->id
                );
            }
        }

        $this->notificationService->orderCancelled($order->user_id, $order->order_number, $refundedPoints);
    }

    /**
     * Generate a unique order number.
     */
    protected function generateOrderNumber(): string
    {
        do {
            $number = 'ORD-' . strtoupper(Str::random(8));
        } while (Order::where('order_number', $number)->exists());

        return $number;
    }
}
