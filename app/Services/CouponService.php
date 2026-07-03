<?php

namespace App\Services;

use App\Models\Coupon;
use App\Models\CouponRedemption;
use Illuminate\Support\Facades\DB;

class CouponService
{
    public function __construct(
        protected PointsService      $pointsService,
        protected NotificationService $notificationService
    ) {}

    /**
     * Redeem a coupon for a user.
     *
     * @throws \Exception on any validation failure
     */
    public function redeem(int $userId, string $code): CouponRedemption
    {
        return DB::transaction(function () use ($userId, $code) {
            $coupon = Coupon::where('code', strtoupper(trim($code)))->lockForUpdate()->firstOrFail();

            // Validation
            if ($coupon->status !== 'active') {
                throw new \Exception(__('This coupon is not active.'));
            }

            if ($coupon->isExpired()) {
                throw new \Exception(__('This coupon has expired.'));
            }

            if ($coupon->isUsageLimitReached()) {
                throw new \Exception(__('This coupon has reached its usage limit.'));
            }

            if (CouponRedemption::where('coupon_id', $coupon->id)->where('user_id', $userId)->exists()) {
                throw new \Exception(__('You have already redeemed this coupon.'));
            }

            // Credit points
            $this->pointsService->credit(
                $userId,
                $coupon->reward_points,
                'coupon',
                __('Coupon redeemed: :code', ['code' => $coupon->code]),
                'coupon',
                $coupon->id
            );

            // Increment used count
            $coupon->increment('used_count');

            // Log redemption
            $redemption = CouponRedemption::create([
                'coupon_id'      => $coupon->id,
                'user_id'        => $userId,
                'points_awarded' => $coupon->reward_points,
                'redeemed_at'    => now(),
            ]);

            // Notify
            $this->notificationService->couponRedeemed($userId, $coupon->reward_points);

            return $redemption;
        });
    }
}
