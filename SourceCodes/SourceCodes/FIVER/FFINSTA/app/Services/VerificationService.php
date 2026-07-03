<?php

namespace App\Services;

use App\Models\InstagramAccount;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class VerificationService
{
    /**
     * Helper to call a task with up to 3 retries.
     */
    protected function retryCall(callable $callback, int $attempts = 3, int $delayMs = 500)
    {
        for ($i = 0; $i < $attempts; $i++) {
            try {
                $result = $callback();
                if ($result !== null) {
                    return $result;
                }
            } catch (\Exception $e) {
                Log::warning("Instagram fetch attempt " . ($i + 1) . " failed: " . $e->getMessage());
            }
            if ($i < $attempts - 1) {
                usleep($delayMs * 1000); // Wait 500ms before retrying
            }
        }
        return null;
    }

    /**
     * Get following count of a user (A) using the 3 methods in order, with 3 retries each.
     */
    public function getFollowingCount(InstagramAccount $account): ?int
    {
        // Method 1: Web Client Profile Info
        $count = $this->retryCall(fn() => $this->getCountViaWebProfileInfo($account->username, 'following'));
        if ($count !== null) {
            Log::info("Got following count for {$account->username} via Web Client API: {$count}");
            return $count;
        }

        // Method 2: Instagram Graph API (if OAuth tokens exist)
        $count = $this->retryCall(fn() => $this->getCountViaGraphApi($account, 'following'));
        if ($count !== null) {
            Log::info("Got following count for {$account->username} via Graph API: {$count}");
            return $count;
        }

        // Method 3: RapidAPI
        $count = $this->retryCall(fn() => $this->getCountViaRapidApi($account->username, 'following'));
        if ($count !== null) {
            Log::info("Got following count for {$account->username} via RapidAPI: {$count}");
            return $count;
        }

        return null;
    }

    /**
     * Get follower count of a user (B) using the 3 methods in order, with 3 retries each.
     */
    public function getFollowerCount(string $username, ?InstagramAccount $account = null): ?int
    {
        // Method 1: Web Client Profile Info
        $count = $this->retryCall(fn() => $this->getCountViaWebProfileInfo($username, 'follower'));
        if ($count !== null) {
            Log::info("Got follower count for {$username} via Web Client API: {$count}");
            return $count;
        }

        // Method 2: Instagram Graph API (if account model provided and has OAuth tokens)
        if ($account) {
            $count = $this->retryCall(fn() => $this->getCountViaGraphApi($account, 'follower'));
            if ($count !== null) {
                Log::info("Got follower count for {$username} via Graph API: {$count}");
                return $count;
            }
        }

        // Method 3: RapidAPI
        $count = $this->retryCall(fn() => $this->getCountViaRapidApi($username, 'follower'));
        if ($count !== null) {
            Log::info("Got follower count for {$username} via RapidAPI: {$count}");
            return $count;
        }

        return null;
    }

    /**
     * Method 1: Fetch follower/following count via Instagram's web_profile_info public endpoint.
     */
    public function getCountViaWebProfileInfo(string $username, string $type = 'follower'): ?int
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://www.instagram.com/api/v1/users/web_profile_info/?username={$username}");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
            'x-ig-app-id: 936619743392459',
            'Accept: */*'
        ]);
        
        $responseBody = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 200) {
            throw new Exception("Web Profile API returned HTTP status $httpCode");
        }

        $data = json_decode($responseBody, true);
        if (isset($data['data']['user'])) {
            $user = $data['data']['user'];
            if ($type === 'following') {
                return isset($user['edge_follow']['count']) ? (int) $user['edge_follow']['count'] : null;
            } else {
                return isset($user['edge_followed_by']['count']) ? (int) $user['edge_followed_by']['count'] : null;
            }
        }

        throw new Exception("No user data found in Web Profile API response.");
    }

    /**
     * Method 2: Fetch follower/following count via Instagram Graph API.
     */
    public function getCountViaGraphApi(InstagramAccount $account, string $type = 'following'): ?int
    {
        if (empty($account->oauth_access_token)) {
            return null;
        }

        $response = Http::timeout(10)->get("https://graph.instagram.com/v21.0/me", [
            'fields' => 'followers_count,follows_count',
            'access_token' => $account->oauth_access_token
        ]);

        if ($response->successful()) {
            if ($type === 'following') {
                return $response->json('follows_count');
            } else {
                return $response->json('followers_count');
            }
        }

        throw new Exception("Graph API returned status: " . $response->status());
    }

    /**
     * Method 3: Fetch follower/following count via RapidAPI.
     */
    public function getCountViaRapidApi(string $username, string $type = 'follower'): ?int
    {
        $rapidApiKey = app(\App\Services\SettingService::class)->get('rapidapi_key') ?? config('services.rapidapi.key');
        $rapidApiHost = app(\App\Services\SettingService::class)->get('rapidapi_host') ?? 'instagram-scraper-api2.p.rapidapi.com';
        
        if (empty($rapidApiKey)) {
            throw new Exception("RapidAPI key not configured.");
        }

        $response = Http::timeout(10)->withHeaders([
            'X-RapidAPI-Host' => $rapidApiHost,
            'X-RapidAPI-Key' => $rapidApiKey,
        ])->get("https://{$rapidApiHost}/v1/info", [
            'username_or_id_or_url' => $username,
        ]);

        if ($response->successful()) {
            $key = ($type === 'following') ? 'data.following_count' : 'data.follower_count';
            $count = $response->json($key);
            if (is_numeric($count)) {
                return (int) $count;
            }
        }

        throw new Exception("RapidAPI returned status: " . $response->status());
    }

    /**
     * List-check fallback verification (checks if followerUsername is in targetUsername's follower list).
     */
    public function verifyFollow(string $followerUsername, string $targetUsername): bool
    {
        $rapidApiKey = app(\App\Services\SettingService::class)->get('rapidapi_key') ?? config('services.rapidapi.key');
        $rapidApiHost = app(\App\Services\SettingService::class)->get('rapidapi_host') ?? 'instagram-scraper-api2.p.rapidapi.com';
        
        if (empty($rapidApiKey)) {
            return false;
        }

        try {
            $response = Http::timeout(15)->withHeaders([
                'X-RapidAPI-Host' => $rapidApiHost,
                'X-RapidAPI-Key' => $rapidApiKey,
            ])->get("https://{$rapidApiHost}/v1/followers", [
                'username_or_id_or_url' => $targetUsername,
            ]);

            if ($response->successful()) {
                $followers = $response->json('data.items');
                if (is_array($followers)) {
                    foreach ($followers as $follower) {
                        if (isset($follower['username']) && strtolower($follower['username']) === strtolower($followerUsername)) {
                            return true;
                        }
                    }
                }
            }
        } catch (\Exception $e) {
            Log::warning("RapidAPI List verification failed: " . $e->getMessage());
        }

        return false;
    }
}
