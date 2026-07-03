<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InstagramAccount extends Model
{
    protected $fillable = [
        'user_id',
        'username',
        'profile_picture_url',
        'instagram_user_id',
        'cookies',
        'user_agent',
        'status',
        'is_default',
        'last_login',
        'oauth_access_token',
        'oauth_refresh_token',
        'oauth_expires_at',
    ];

    protected $casts = [
        'is_default'          => 'boolean',
        'last_login'          => 'datetime',
        'cookies'             => 'encrypted',
        'oauth_access_token'  => 'encrypted',
        'oauth_refresh_token' => 'encrypted',
        'oauth_expires_at'    => 'datetime',
    ];

    // ─── Relationships ────────────────────────────────────────────────────────

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    // ─── Helpers ─────────────────────────────────────────────────────────────

    public function isActive(): bool
    {
        return $this->status === 'active';
    }
}
