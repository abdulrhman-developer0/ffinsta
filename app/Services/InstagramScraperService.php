<?php

namespace App\Services;

use App\Models\InstagramAccount;
use App\Models\InstagramProfileCache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class InstagramScraperService
{
    public function __construct(protected SettingService $settingService)
    {
    }

    public function fetchProfilePicture(string $username)
    {
        $username = trim($username);

        // 0. Check Central Cache Table
        $cachedProfile = InstagramProfileCache::where('username', $username)->first();
        if ($cachedProfile) {
            // Ensure any existing account gets updated too just in case
            InstagramAccount::where('username', $username)
                ->whereNull('profile_picture_url')
                ->update(['profile_picture_url' => $cachedProfile->profile_picture_url]);

            return [
                'success' => true,
                'image' => $cachedProfile->profile_picture_url,
                'source' => 'database_cache'
            ];
        }

        // 1. Check if an InstagramAccount exists with a profile picture
        $existingAccount = InstagramAccount::where('username', $username)
            ->whereNotNull('profile_picture_url')
            ->first();

        if ($existingAccount) {
            // Backfill the cache table
            InstagramProfileCache::updateOrCreate(
                ['username' => $username],
                ['profile_picture_url' => $existingAccount->profile_picture_url]
            );

            return [
                'success' => true,
                'image' => $existingAccount->profile_picture_url,
                'source' => 'database_account'
            ];
        }

        // 2. Try RapidAPIs
        $rapidApiKey = $this->settingService->get('rapidapi_key') ?? config('services.rapidapi.key');
        $rapidApiHost = $this->settingService->get('rapidapi_host') ?? 'instagram-looter2.p.rapidapi.com';
        
        $lastError = 'RapidAPI key is not configured.';

        if (!empty($rapidApiKey) && !empty($rapidApiHost)) {
            $url = "https://{$rapidApiHost}/search?query=" . urlencode($username);

            try {
                $response = Http::withHeaders([
                    'X-RapidAPI-Key' => $rapidApiKey,
                    'X-RapidAPI-Host' => $rapidApiHost,
                ])->get($url);

                if ($response->successful()) {
                    $data = $response->json();
                    $profilePicUrl = $this->findProfilePicUrl($data);

                    if ($profilePicUrl) {
                        // Save to database & Cache
                        InstagramAccount::where('username', $username)->update(['profile_picture_url' => $profilePicUrl]);
                        InstagramProfileCache::updateOrCreate(
                            ['username' => $username],
                            ['profile_picture_url' => $profilePicUrl]
                        );

                        return [
                            'success' => true,
                            'image' => $profilePicUrl,
                            'source' => $rapidApiHost
                        ];
                    } else {
                        $lastError = "Profile picture not found in API response from $rapidApiHost.";
                        Log::error('Could not find profile picture URL in RapidAPI response.', ['host' => $rapidApiHost, 'data' => $data]);
                    }
                } else {
                    $lastError = "API request failed for $rapidApiHost: " . $response->status();
                    Log::error('RapidAPI request failed.', ['host' => $rapidApiHost, 'status' => $response->status(), 'response' => $response->body()]);
                }
            } catch (\Exception $e) {
                $lastError = "Exception during RapidAPI request for $rapidApiHost: " . $e->getMessage();
                Log::error($lastError);
            }
        }

        // 3. Fallback to Bing
        return $this->fallbackToBing($username, $lastError);
    }

    protected function fallbackToBing(string $username, string $errorMessage)
    {
        try {
            $query = urlencode("instagram {$username} profile picture");
            $html = Http::withHeaders([
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'
            ])->get("https://www.bing.com/images/search?q={$query}")->body();
            
            if (preg_match_all('/murl&quot;:&quot;(.*?)&quot;/', $html, $matches)) {
                $images = array_unique($matches[1]);
                $firstImage = array_values($images)[0] ?? null;
                
                if ($firstImage) {
                    // Save to database & Cache
                    InstagramAccount::where('username', $username)->update(['profile_picture_url' => $firstImage]);
                    InstagramProfileCache::updateOrCreate(
                        ['username' => $username],
                        ['profile_picture_url' => $firstImage]
                    );

                    return [
                        'success' => true,
                        'image' => $firstImage,
                        'source' => 'bing_fallback'
                    ];
                }
            }
        } catch (\Exception $e) {
            Log::error('Bing fallback failed: ' . $e->getMessage());
        }

        return [
            'success' => false,
            'message' => $errorMessage
        ];
    }

    protected function findProfilePicUrl(array $data): ?string
    {
        $keysToLookFor = ['profile_pic_url_hd', 'profile_pic_url', 'profile_picture_url', 'avatar_url', 'avatar'];

        foreach ($keysToLookFor as $key) {
            if (isset($data[$key]) && is_string($data[$key]) && filter_var($data[$key], FILTER_VALIDATE_URL)) {
                return $data[$key];
            }
        }

        foreach ($data as $value) {
            if (is_array($value)) {
                $found = $this->findProfilePicUrl($value);
                if ($found) {
                    return $found;
                }
            }
        }

        return null;
    }
}
