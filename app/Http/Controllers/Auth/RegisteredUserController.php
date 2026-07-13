<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Referral;
use App\Models\User;
use App\Services\PointsService;
use App\Services\SettingService;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Coderflex\LaravelTurnstile\Rules\TurnstileCheck;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    public function __construct(
        protected SettingService $settingService,
        protected PointsService  $pointsService
    ) {}

    /**
     * Display the registration view.
     */
    public function create(Request $request): View|RedirectResponse
    {
        // Check if registration is enabled
        if (!$this->settingService->isEnabled('registration_enabled')) {
            return redirect()->route('login')->withErrors(['registration' => __('Registration is currently disabled.')]);
        }

        return view('auth.register', [
            'referralCode' => $request->get('ref', ''),
        ]);
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        // Check if registration is enabled
        if (!$this->settingService->isEnabled('registration_enabled')) {
            abort(403, __('Registration is currently disabled.'));
        }

        $request->validate([
            'name'          => ['required', 'string', 'max:255'],
            'email'         => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password'      => ['required', 'confirmed', Rules\Password::defaults()],
            'referral_code' => ['nullable', 'string', 'max:20'],
            'cf-turnstile-response' => [
                'required',
                new TurnstileCheck(),
            ],
        ]);

        // Resolve referrer
        $referrer = null;
        if ($request->filled('referral_code')) {
            $referrer = User::where('referral_code', strtoupper($request->referral_code))->first();
        }

        $user = User::create([
            'name'          => $request->name,
            'email'         => $request->email,
            'password'      => Hash::make($request->password),
            'role'          => 'user',
            'points'        => 0,
            'referral_code' => strtoupper(Str::random(8)),
            'referred_by'   => $referrer?->id,
            'is_suspended'  => false,
        ]);

        // Award referral bonus to referrer
        if ($referrer) {
            $bonusPoints = (int) $this->settingService->get('referral_bonus_points', 50);
            if ($bonusPoints > 0) {
                $this->pointsService->credit(
                    $referrer->id,
                    $bonusPoints,
                    'referral',
                    __('Referral bonus — :name joined using your link', ['name' => $user->name]),
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

        event(new Registered($user));

        Auth::login($user);

        return redirect()->route('user.dashboard');
    }
}
