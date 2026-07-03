<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Coupon extends Model
{
    protected $fillable = [
        'code',
        'reward_points',
        'usage_limit',
        'used_count',
        'expires_at',
        'status',
    ];

    protected $casts = [
        'reward_points' => 'integer',
        'usage_limit'   => 'integer',
        'used_count'    => 'integer',
        'expires_at'    => 'datetime',
    ];

    // ─── Relationships ────────────────────────────────────────────────────────

    public function redemptions(): HasMany
    {
        return $this->hasMany(CouponRedemption::class);
    }

    // ─── Helpers ─────────────────────────────────────────────────────────────

    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    public function isUsageLimitReached(): bool
    {
        return $this->usage_limit > 0 && $this->used_count >= $this->usage_limit;
    }

    public function isRedeemableBy(int $userId): bool
    {
        if ($this->status !== 'active') return false;
        if ($this->isExpired()) return false;
        if ($this->isUsageLimitReached()) return false;
        if ($this->redemptions()->where('user_id', $userId)->exists()) return false;
        return true;
    }
}
