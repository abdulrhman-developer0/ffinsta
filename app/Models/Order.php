<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    protected $fillable = [
        'order_number',
        'user_id',
        'instagram_account_id',
        'instagram_username',
        'profile_picture_url',
        'requested_qty',
        'delivered_qty',
        'points_cost',
        'status',
        'priority',
        'admin_notes',
    ];

    protected $casts = [
        'requested_qty' => 'integer',
        'delivered_qty' => 'integer',
        'points_cost'   => 'integer',
    ];

    // ─── Relationships ────────────────────────────────────────────────────────

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function instagramAccount(): BelongsTo
    {
        return $this->belongsTo(InstagramAccount::class);
    }

    public function followTasks(): HasMany
    {
        return $this->hasMany(FollowTask::class, 'requester_order_id');
    }

    // ─── Helpers ─────────────────────────────────────────────────────────────

    public function progressPercent(): int
    {
        if ($this->requested_qty === 0) return 0;
        return (int) min(100, round(($this->delivered_qty / $this->requested_qty) * 100));
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }
}
