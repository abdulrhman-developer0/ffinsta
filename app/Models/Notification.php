<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notification extends Model
{
    protected $fillable = [
        'user_id',
        'type',
        'title',
        'message',
        'is_read',
    ];

    protected $casts = [
        'is_read' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // ─── Helpers ─────────────────────────────────────────────────────────────

    public function iconClass(): string
    {
        return match($this->type) {
            'points_added'    => 'text-green-500',
            'points_deducted' => 'text-red-500',
            'coupon_redeemed' => 'text-yellow-500',
            'order_approved', 'order_activated', 'order_completed' => 'text-blue-500',
            'order_cancelled' => 'text-red-500',
            default           => 'text-brand-500',
        };
    }
}
