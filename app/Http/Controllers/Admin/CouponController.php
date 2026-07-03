<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use App\Services\ActivityLogService;
use Illuminate\Http\Request;

class CouponController extends Controller
{
    public function __construct(protected ActivityLogService $activityLogService) {}

    public function index(Request $request)
    {
        $coupons = Coupon::withCount('redemptions')
            ->when($request->search, fn($q, $s) => $q->where('code', 'like', "%$s%"))
            ->when($request->status, fn($q, $s) => $q->where('status', $s))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.coupons.index', compact('coupons'));
    }

    public function create()
    {
        return view('admin.coupons.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'code'          => ['required', 'string', 'max:50', 'unique:coupons,code'],
            'reward_points' => ['required', 'integer', 'min:1'],
            'usage_limit'   => ['nullable', 'integer', 'min:0'],
            'expires_at'    => ['nullable', 'date', 'after:now'],
            'status'        => ['required', 'in:active,inactive'],
        ]);

        $coupon = Coupon::create([
            'code'          => strtoupper($request->code),
            'reward_points' => $request->reward_points,
            'usage_limit'   => $request->usage_limit ?? 0,
            'used_count'    => 0,
            'expires_at'    => $request->expires_at,
            'status'        => $request->status,
        ]);

        $this->activityLogService->log('create_coupon', "Created coupon: {$coupon->code}", 'coupon', $coupon->id);

        return redirect()->route('admin.coupons.index')->with('success', __('Coupon created.'));
    }

    public function show(Coupon $coupon)
    {
        $coupon->load('redemptions.user');
        return view('admin.coupons.show', compact('coupon'));
    }

    public function edit(Coupon $coupon)
    {
        return view('admin.coupons.edit', compact('coupon'));
    }

    public function update(Request $request, Coupon $coupon)
    {
        $request->validate([
            'reward_points' => ['required', 'integer', 'min:1'],
            'usage_limit'   => ['nullable', 'integer', 'min:0'],
            'expires_at'    => ['nullable', 'date'],
            'status'        => ['required', 'in:active,inactive'],
        ]);

        $coupon->update([
            'reward_points' => $request->reward_points,
            'usage_limit'   => $request->usage_limit ?? 0,
            'expires_at'    => $request->expires_at,
            'status'        => $request->status,
        ]);

        $this->activityLogService->log('update_coupon', "Updated coupon: {$coupon->code}", 'coupon', $coupon->id);

        return redirect()->route('admin.coupons.index')->with('success', __('Coupon updated.'));
    }

    public function destroy(Coupon $coupon)
    {
        $this->activityLogService->log('delete_coupon', "Deleted coupon: {$coupon->code}", 'coupon', $coupon->id);
        $coupon->delete();

        return redirect()->route('admin.coupons.index')->with('success', __('Coupon deleted.'));
    }
}
