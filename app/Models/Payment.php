<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'package_id',
        'amount_egp',
        'amount_usd',
        'points',
        'payment_method',
        'sender_phone',
        'transaction_id',
        'status',
    ];

    protected $casts = [
        'amount_egp' => 'decimal:2',
        'amount_usd' => 'decimal:2',
        'points'     => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function package(): BelongsTo
    {
        return $this->belongsTo(Package::class);
    }
}
