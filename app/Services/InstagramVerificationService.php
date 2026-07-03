<?php

namespace App\Services;

use App\Models\InstagramAccount;

class InstagramVerificationService
{
    /**
     * Optimistic approach: store credentials without real-time verification.
     * Admin manually validates accounts.
     *
     * Returns true if username appears valid (basic format check).
     */
    public function verify(string $username): bool
    {
        // Basic Instagram username format validation
        // Usernames: 1–30 chars, letters/numbers/periods/underscores
        return (bool) preg_match('/^[a-zA-Z0-9._]{1,30}$/', $username);
    }

    /**
     * Store an Instagram account for a user (optimistic).
     */
    public function store(
        int    $userId,
        string $username,
        string $cookies = null,
        string $userAgent = null,
        string $profilePictureUrl = null
    ): InstagramAccount {
        // Set all other accounts as non-default if this is the first
        $isFirst = !InstagramAccount::where('user_id', $userId)->exists();

        $account = InstagramAccount::create([
            'user_id'    => $userId,
            'username'   => strtolower(trim($username)),
            'profile_picture_url' => $profilePictureUrl,
            'cookies'    => $cookies,
            'user_agent' => $userAgent,
            'status'     => 'active',
            'is_default' => $isFirst,
        ]);

        return $account;
    }

    /**
     * Set an account as the default for a user.
     */
    public function setDefault(int $userId, int $accountId): void
    {
        InstagramAccount::where('user_id', $userId)->update(['is_default' => false]);
        InstagramAccount::where('id', $accountId)->where('user_id', $userId)->update(['is_default' => true]);
    }
}
