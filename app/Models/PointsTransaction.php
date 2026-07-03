<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PointsTransaction extends Model
{
    protected $fillable = [
        'user_id',
        'type',
        'amount',
        'balance_after',
        'description',
        'reference_type',
        'reference_id',
    ];

    protected $casts = [
        'amount'       => 'integer',
        'balance_after' => 'integer',
        'reference_id' => 'integer',
    ];

    // ─── Relationships ────────────────────────────────────────────────────────

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // ─── Helpers ─────────────────────────────────────────────────────────────

    public function isCredit(): bool
    {
        return in_array($this->type, ['earn', 'coupon', 'referral', 'admin_adjustment']) && $this->amount > 0;
    }

    public function typeLabel(): string
    {
        return match($this->type) {
            'earn'             => __('Task Reward'),
            'spend'            => __('Order Spend'),
            'coupon'           => __('Coupon Redemption'),
            'referral'         => __('Referral Bonus'),
            'admin_adjustment' => __('Admin Adjustment'),
            default            => ucfirst($this->type),
        };
    }

    public function typeBadgeClass(): string
    {
        return match($this->type) {
            'earn', 'coupon', 'referral' => 'badge badge-completed',
            'spend'                      => 'badge badge-cancelled',
            'admin_adjustment'           => 'badge badge-active',
            default                      => 'badge badge-normal',
        };
    }
}
