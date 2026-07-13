<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Referral;
use App\Models\User;
use App\Services\PointsService;
use App\Services\SettingService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class GoogleController extends Controller
{
    public function __construct(
        protected SettingService $settingService,
        protected PointsService $pointsService
    ) {}

    public function redirect(?string $referral = null)
    {
        if ($referral) {
            session(['referral_code' => strtoupper($referral)]);
        }

        return Socialite::driver('google')->redirect();
    }

    public function callback()
    {
        $googleUser = Socialite::driver('google')->user();

        $user = User::where('email', $googleUser->getEmail())->first();

        if (!$user) {

            if (!$this->settingService->isEnabled('registration_enabled')) {
                abort(403, __('Registration is currently disabled.'));
            }

            $referrer = null;

            $referralCode = session('referral_code');

            if ($referralCode) {
                $referrer = User::where(
                    'referral_code',
                    strtoupper($referralCode)
                )->first();
            }

            $user = User::create([
                'name'              => $googleUser->getName(),
                'email'             => $googleUser->getEmail(),
                'google_id'         => $googleUser->getId(),
                'password'          => bcrypt(Str::random(32)),
                'email_verified_at' => now(),
                'role'              => 'user',
                'points'            => 0,
                'referral_code'     => strtoupper(Str::random(8)),
                'referred_by'       => $referrer?->id,
                'is_suspended'      => false,
            ]);

            if ($referrer) {

                $bonusPoints = (int) $this->settingService
                    ->get('referral_bonus_points', 50);

                if ($bonusPoints > 0) {
                    $this->pointsService->credit(
                        $referrer->id,
                        $bonusPoints,
                        'referral',
                        __('Referral bonus — :name joined using your link', [
                            'name' => $user->name
                        ]),
                        'user',
                        $user->id
                    );
                }

                Referral::create([
                    'referrer_id'    => $referrer->id,
                    'referee_id'     => $user->id,
                    'points_awarded' => $bonusPoints,
                ]);
            }
        } else {

            if (!$user->google_id) {
                $user->update([
                    'google_id' => $googleUser->getId(),
                ]);
            }
        }

        session()->forget('referral_code');

        Auth::login($user, true);

        return redirect()->route('user.dashboard');
    }
}