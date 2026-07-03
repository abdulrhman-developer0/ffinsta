<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\InstagramAccount;
use App\Services\InstagramVerificationService;
use App\Services\SettingService;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;

class InstagramAccountController extends Controller
{
    public function __construct(
        protected InstagramVerificationService $verificationService,
        protected SettingService               $settingService
    ) {}

    public function index()
    {
        $accounts = InstagramAccount::where('user_id', auth()->id())
            ->latest()
            ->get();

        return view('user.instagram.index', compact('accounts'));
    }

    public function create()
    {
        return view('user.instagram.create');
    }

    public function store(Request $request)
    {
        $authMethod = $this->settingService->get('instagram_auth_method', 'simple');
        if ($authMethod === 'oauth') {
            return back()->withErrors(['username' => __('Adding accounts manually is disabled. Please connect with Instagram.')]);
        }

        $request->validate([
            'username' => ['required', 'string', 'max:30', 'regex:/^[a-zA-Z0-9._]{1,30}$/'],
            'profile_picture_url' => ['nullable', 'url', 'max:1000'],
        ]);

        $userId  = auth()->id();
        $maxAccounts = (int) $this->settingService->get('max_instagram_accounts', 5);

        // Check account limit
        $currentCount = InstagramAccount::where('user_id', $userId)->count();
        if ($currentCount >= $maxAccounts) {
            return back()->withErrors(['username' => __('You can only add up to :max Instagram accounts.', ['max' => $maxAccounts])]);
        }

        // Check username not already added globally
        $existingAccount = InstagramAccount::with('user')->where('username', strtolower($request->username))->first();
        if ($existingAccount) {
            if ($existingAccount->user_id === $userId) {
                return back()->withErrors(['username' => __('This Instagram account is already connected by you.')]);
            } else {
                return back()->withErrors(['username' => __('This Instagram account is already linked with :email', ['email' => $existingAccount->user->email ?? 'another user'])]);
            }
        }

        $account = $this->verificationService->store($userId, $request->username, null, null, $request->profile_picture_url);

        if (!empty($request->profile_picture_url)) {
            \App\Models\InstagramProfileCache::updateOrCreate(
                ['username' => strtolower($request->username)],
                ['profile_picture_url' => $request->profile_picture_url]
            );
        }

        return redirect()->route('user.instagram.index')
            ->with('success', __('Instagram account @:username added successfully.', ['username' => $account->username]));
    }

    public function edit(InstagramAccount $account)
    {
        abort_if($account->user_id !== auth()->id(), 403);
        return view('user.instagram.edit', compact('account'));
    }

    public function update(Request $request, InstagramAccount $account)
    {
        abort_if($account->user_id !== auth()->id(), 403);

        $request->validate([
            'username' => ['required', 'string', 'max:30', 'regex:/^[a-zA-Z0-9._]{1,30}$/'],
        ]);

        $newUsername = strtolower($request->username);
        
        if ($newUsername !== $account->username) {
            $existingAccount = InstagramAccount::with('user')->where('username', $newUsername)->first();
            if ($existingAccount) {
                if ($existingAccount->user_id === auth()->id()) {
                    return back()->withErrors(['username' => __('This Instagram account is already connected by you.')]);
                } else {
                    return back()->withErrors(['username' => __('This Instagram account is already linked with :email', ['email' => $existingAccount->user->email ?? 'another user'])]);
                }
            }
        }

        $account->update([
            'username' => strtolower($request->username),
            'status'   => 'active',
        ]);

        return redirect()->route('user.instagram.index')
            ->with('success', __('Account updated successfully.'));
    }

    public function destroy(InstagramAccount $account)
    {
        abort_if($account->user_id !== auth()->id(), 403);

        // Cancel any active tasks assigned to this account
        $account->delete();

        return redirect()->route('user.instagram.index')
            ->with('success', __('Instagram account removed.'));
    }

    public function setDefault(InstagramAccount $account)
    {
        abort_if($account->user_id !== auth()->id(), 403);

        $this->verificationService->setDefault(auth()->id(), $account->id);

        return back()->with('success', __('@:username is now your default account.', ['username' => $account->username]));
    }

    public function oauthRedirect()
    {
        $authMethod = $this->settingService->get('instagram_auth_method', 'simple');
        if ($authMethod !== 'oauth') {
            return redirect()->route('user.instagram.create')->withErrors(['oauth' => __('OAuth login is currently disabled.')]);
        }

        return Socialite::driver('instagram')->redirect();
    }

    public function oauthCallback()
    {
        $authMethod = $this->settingService->get('instagram_auth_method', 'simple');
        if ($authMethod !== 'oauth') {
            return redirect()->route('user.instagram.create')->withErrors(['oauth' => __('OAuth login is currently disabled.')]);
        }

        try {
            $instagramUser = Socialite::driver('instagram')->user();
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Instagram OAuth Error: " . $e->getMessage() . "\n" . $e->getTraceAsString());
            $friendlyMessage = __('Instagram OAuth connection failed. Your Meta Developer App is currently under review or restricted. Please ensure your app is live and fully approved, or that you are using a properly configured tester account in the Meta Developer Dashboard.');
            return redirect()->route('user.instagram.create')->withErrors(['oauth' => $friendlyMessage]);
        }

        // Instagram returns nickname or username
        $username = $instagramUser->nickname ?? $instagramUser->name ?? $instagramUser->id;
        
        if (!$username) {
            return redirect()->route('user.instagram.create')->withErrors(['oauth' => __('Could not retrieve username from Instagram.')]);
        }

        $userId = auth()->id();
        $maxAccounts = (int) $this->settingService->get('max_instagram_accounts', 5);

        // Check account limit
        $currentCount = InstagramAccount::where('user_id', $userId)->count();
        if ($currentCount >= $maxAccounts) {
            return redirect()->route('user.instagram.index')->withErrors(['username' => __('You can only add up to :max Instagram accounts.', ['max' => $maxAccounts])]);
        }

        // Check username not already added globally
        $existingAccount = InstagramAccount::with('user')->where('username', strtolower($username))->first();
        if ($existingAccount) {
            if ($existingAccount->user_id === $userId) {
                return redirect()->route('user.instagram.index')->withErrors(['username' => __('This Instagram account is already connected by you.')]);
            } else {
                return redirect()->route('user.instagram.index')->withErrors(['username' => __('This Instagram account is already linked with :email', ['email' => $existingAccount->user->email ?? 'another user'])]);
            }
        }

        $profilePicUrl = $instagramUser->avatar;
        
        if (empty($profilePicUrl)) {
            // Try to fetch from public API
            try {
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

                if ($httpCode === 200) {
                    $data = json_decode($responseBody, true);
                    if (isset($data['data']['user'])) {
                        $user = $data['data']['user'];
                        $profilePicUrl = $user['profile_pic_url_hd'] ?? $user['profile_pic_url'] ?? null;
                    }
                }
            } catch (\Exception $e) {
                // Ignore error and proceed without profile picture
            }
        }

        // Proceed to store via verification service
        $account = $this->verificationService->store($userId, $username, null, null, $profilePicUrl);

        // Update the account with OAuth specific details
        $account->update([
            'instagram_user_id'   => $instagramUser->id,
            'profile_picture_url' => $profilePicUrl ?? $account->profile_picture_url,
            'oauth_access_token'  => $instagramUser->token,
            'oauth_refresh_token' => $instagramUser->refreshToken ?? null,
            'oauth_expires_at'    => isset($instagramUser->expiresIn) ? now()->addSeconds($instagramUser->expiresIn) : null,
        ]);

        if (!empty($profilePicUrl)) {
            \App\Models\InstagramProfileCache::updateOrCreate(
                ['username' => strtolower($username)],
                ['profile_picture_url' => $profilePicUrl]
            );
        }

        return redirect()->route('user.instagram.index')
            ->with('success', __('Instagram account @:username added successfully.', ['username' => $account->username]));
    }

    public function verifyProfile(Request $request)
    {
        $request->validate([
            'username' => ['required', 'string', 'max:30', 'regex:/^[a-zA-Z0-9._]{1,30}$/'],
        ]);

        $username = strtolower($request->username);

        try {
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

            if ($httpCode === 404) {
                return response()->json(['error' => __('Profile not found.')], 404);
            }

            $data = json_decode($responseBody, true);

            if (!isset($data['data']['user'])) {
                 return response()->json([
                    'error' => __('Could not fetch profile data. The account might be private or Instagram blocked the request.'),
                    'allow_force_add' => true,
                ], 400);
            }

            $user = $data['data']['user'];
            
            $profilePicUrl = $user['profile_pic_url_hd'] ?? $user['profile_pic_url'] ?? null;
            
            // Format stats
            $followers = number_format($user['edge_followed_by']['count'] ?? 0);
            $following = number_format($user['edge_follow']['count'] ?? 0);
            $posts = number_format($user['edge_owner_to_timeline_media']['count'] ?? 0);
            
            $statsString = "{$followers} Followers, {$following} Following, {$posts} Posts";

            return response()->json([
                'success' => true,
                'profile_pic_url' => $profilePicUrl,
                'stats_string' => $statsString,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => __('Failed to connect to Instagram to verify the profile.'),
                'allow_force_add' => true,
            ], 500);
        }
    }
}
