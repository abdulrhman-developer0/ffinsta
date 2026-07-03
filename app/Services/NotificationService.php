<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;

class NotificationService
{
    /**
     * Send a notification to a user.
     */
    public function send(
        int    $userId,
        string $type,
        string $title,
        string $message
    ): Notification {
        return Notification::create([
            'user_id' => $userId,
            'type'    => $type,
            'title'   => $title,
            'message' => $message,
            'is_read' => false,
        ]);
    }

    /**
     * Mark a single notification as read.
     */
    public function markRead(int $notificationId, int $userId): bool
    {
        return (bool) Notification::where('id', $notificationId)
            ->where('user_id', $userId)
            ->update(['is_read' => true]);
    }

    /**
     * Mark all notifications for a user as read.
     */
    public function markAllRead(int $userId): void
    {
        Notification::where('user_id', $userId)
            ->where('is_read', false)
            ->update(['is_read' => true]);
    }

    /**
     * Get unread count for a user.
     */
    public function unreadCount(int $userId): int
    {
        return Notification::where('user_id', $userId)
            ->where('is_read', false)
            ->count();
    }

    // ─── Convenience methods ───────────────────────────────────────────────────

    public function pointsAdded(int $userId, int $amount, string $description = ''): void
    {
        $this->send($userId, 'points_added', __('Points Added'), __(':amount points have been added to your account. :desc', ['amount' => $amount, 'desc' => $description]));
    }

    public function pointsDeducted(int $userId, int $amount, string $description = ''): void
    {
        $this->send($userId, 'points_deducted', __('Points Deducted'), __(':amount points have been deducted from your account. :desc', ['amount' => $amount, 'desc' => $description]));
    }

    public function couponRedeemed(int $userId, int $points): void
    {
        $this->send($userId, 'coupon_redeemed', __('Coupon Redeemed'), __('You successfully redeemed a coupon and earned :points points!', ['points' => $points]));
    }

    public function orderActivated(int $userId, string $orderNumber): void
    {
        $this->send($userId, 'order_activated', __('Order Activated'), __('Your order #:number is now active and followers will start soon.', ['number' => $orderNumber]));
    }

    public function orderCompleted(int $userId, string $orderNumber): void
    {
        $this->send($userId, 'order_completed', __('Order Completed'), __('Your order #:number has been completed!', ['number' => $orderNumber]));
    }

    public function orderCancelled(int $userId, string $orderNumber, int $refundedPoints = 0): void
    {
        $msg = __('Your order #:number has been cancelled.', ['number' => $orderNumber]);
        if ($refundedPoints > 0) {
            $msg .= ' ' . __(':points points have been refunded.', ['points' => $refundedPoints]);
        }
        $this->send($userId, 'order_cancelled', __('Order Cancelled'), $msg);
    }
}
