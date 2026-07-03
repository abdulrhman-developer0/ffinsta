<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Services\CouponService;
use Illuminate\Http\Request;

class CouponController extends Controller
{
    public function __construct(protected CouponService $couponService) {}

    public function index()
    {
        $redemptions = auth()->user()->couponRedemptions()
            ->with('coupon')
            ->latest()
            ->get();

        return view('user.coupons.redeem', compact('redemptions'));
    }

    public function redeem(Request $request)
    {
        $request->validate([
            'code' => ['required', 'string', 'max:50'],
        ]);

        try {
            $redemption = $this->couponService->redeem(auth()->id(), $request->code);
            return back()->with('success', __('Coupon redeemed! :pts points added to your account.', ['pts' => $redemption->points_awarded]));
        } catch (\Exception $e) {
            return back()->withErrors(['code' => $e->getMessage()]);
        }
    }
}
