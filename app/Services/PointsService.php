<?php

namespace App\Services;

use App\Exceptions\InsufficientPointsException;
use App\Models\PointsTransaction;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class PointsService
{
    /**
     * Credit points to a user.
     */
    public function credit(
        int    $userId,
        int    $amount,
        string $type,
        string $description,
        string $referenceType = null,
        int    $referenceId = null
    ): PointsTransaction {
        return DB::transaction(function () use ($userId, $amount, $type, $description, $referenceType, $referenceId) {
            // Lock the user row to prevent race conditions
            $user = User::lockForUpdate()->findOrFail($userId);

            $user->points += $amount;
            $user->save();

            return PointsTransaction::create([
                'user_id'        => $userId,
                'type'           => $type,
                'amount'         => $amount,
                'balance_after'  => $user->points,
                'description'    => $description,
                'reference_type' => $referenceType,
                'reference_id'   => $referenceId,
            ]);
        });
    }

    /**
     * Deduct points from a user. Throws InsufficientPointsException if not enough.
     */
    public function deduct(
        int    $userId,
        int    $amount,
        string $type,
        string $description,
        string $referenceType = null,
        int    $referenceId = null
    ): PointsTransaction {
        return DB::transaction(function () use ($userId, $amount, $type, $description, $referenceType, $referenceId) {
            // Lock the user row to prevent race conditions
            $user = User::lockForUpdate()->findOrFail($userId);

            if ($user->points < $amount) {
                throw new InsufficientPointsException($amount, $user->points);
            }

            $user->points -= $amount;
            $user->save();

            return PointsTransaction::create([
                'user_id'        => $userId,
                'type'           => $type,
                'amount'         => -$amount,
                'balance_after'  => $user->points,
                'description'    => $description,
                'reference_type' => $referenceType,
                'reference_id'   => $referenceId,
            ]);
        });
    }

    /**
     * Admin adjustment (can be positive or negative).
     */
    public function adminAdjust(
        int    $userId,
        int    $amount,
        string $description
    ): PointsTransaction {
        return DB::transaction(function () use ($userId, $amount, $description) {
            $user = User::lockForUpdate()->findOrFail($userId);

            if ($amount < 0 && $user->points < abs($amount)) {
                // Clamp to zero rather than throw for admin adjustments
                $amount = -$user->points;
            }

            $user->points += $amount;
            $user->save();

            return PointsTransaction::create([
                'user_id'        => $userId,
                'type'           => 'admin_adjustment',
                'amount'         => $amount,
                'balance_after'  => $user->points,
                'description'    => $description,
                'reference_type' => null,
                'reference_id'   => null,
            ]);
        });
    }
}
