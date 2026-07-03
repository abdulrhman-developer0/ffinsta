<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FollowTask extends Model
{
    protected $fillable = [
        'requester_order_id',
        'requester_instagram_username',
        'assigned_user_id',
        'instagram_account_id',
        'reward_points',
        'status',
        'initial_follower_count',
        'initial_following_count',
        'verification_type',
        'complete_clicked_at',
        'completed_at',
    ];

    protected $casts = [
        'reward_points' => 'integer',
        'completed_at'  => 'datetime',
        'complete_clicked_at' => 'datetime',
    ];

    // ─── Relationships ────────────────────────────────────────────────────────

    public function instagramAccount(): BelongsTo
    {
        return $this->belongsTo(InstagramAccount::class, 'instagram_account_id');
    }


    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'requester_order_id');
    }

    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_user_id');
    }

    public function completions(): HasMany
    {
        return $this->hasMany(TaskCompletion::class, 'task_id');
    }

    // ─── Helpers ─────────────────────────────────────────────────────────────

    public function isAvailable(): bool
    {
        return $this->status === 'available';
    }
}
