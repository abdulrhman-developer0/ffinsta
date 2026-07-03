<x-guest-layout>
    <x-slot name="title">{{ __('Create Account') }}</x-slot>

    <div class="mb-6 text-center">
        <h2 class="text-xl font-extrabold text-slate-900 dark:text-white">{{ __('Create Account') }}</h2>
        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1.5">{{ __('Sign up for free and get bonus points to start growing') }}</p>
    </div>

    <form method="POST" action="{{ route('register') }}" class="space-y-4">
        @csrf

        {{-- Registration disabled notice --}}
        @error('registration')
            <div class="alert-error mb-4" role="alert">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span>{{ $message }}</span>
            </div>
        @enderror

        {{-- Name --}}
        <div>
            <label for="name" class="form-label">{{ __('Full Name') }}</label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 rtl:left-auto rtl:right-0 pl-3.5 rtl:pl-0 rtl:pr-3.5 flex items-center pointer-events-none text-slate-400 dark:text-slate-500">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                </div>
                <input id="name" type="text" name="name" value="{{ old('name') }}"
                       class="form-input pl-10 rtl:pl-4 rtl:pr-10" required autofocus autocomplete="name"
                       placeholder="{{ __('Your name') }}">
            </div>
            @error('name') <p class="form-error mt-1.5">{{ $message }}</p> @enderror
        </div>

        {{-- Email --}}
        <div>
            <label for="email" class="form-label">{{ __('Email Address') }}</label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 rtl:left-auto rtl:right-0 pl-3.5 rtl:pl-0 rtl:pr-3.5 flex items-center pointer-events-none text-slate-400 dark:text-slate-500">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.206"/>
                    </svg>
                </div>
                <input id="email" type="email" name="email" value="{{ old('email') }}"
                       class="form-input pl-10 rtl:pl-4 rtl:pr-10" required autocomplete="email"
                       placeholder="you@example.com">
            </div>
            @error('email') <p class="form-error mt-1.5">{{ $message }}</p> @enderror
        </div>

        {{-- Password --}}
        <div>
            <label for="password" class="form-label">{{ __('Password') }}</label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 rtl:left-auto rtl:right-0 pl-3.5 rtl:pl-0 rtl:pr-3.5 flex items-center pointer-events-none text-slate-400 dark:text-slate-500">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                </div>
                <input id="password" type="password" name="password"
                       class="form-input pl-10 rtl:pl-4 rtl:pr-10" required autocomplete="new-password"
                       placeholder="••••••••">
            </div>
            @error('password') <p class="form-error mt-1.5">{{ $message }}</p> @enderror
        </div>

        {{-- Confirm Password --}}
        <div>
            <label for="password_confirmation" class="form-label">{{ __('Confirm Password') }}</label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 rtl:left-auto rtl:right-0 pl-3.5 rtl:pl-0 rtl:pr-3.5 flex items-center pointer-events-none text-slate-400 dark:text-slate-500">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                </div>
                <input id="password_confirmation" type="password" name="password_confirmation"
                       class="form-input pl-10 rtl:pl-4 rtl:pr-10" required autocomplete="new-password"
                       placeholder="••••••••">
            </div>
            @error('password_confirmation') <p class="form-error mt-1.5">{{ $message }}</p> @enderror
        </div>

        {{-- Referral Code (optional) --}}
        <div>
            <label for="referral_code" class="form-label">
                {{ __('Referral Code') }}
                <span class="text-muted text-xs font-normal ml-1">{{ __('(Optional)') }}</span>
            </label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 rtl:left-auto rtl:right-0 pl-3.5 rtl:pl-0 rtl:pr-3.5 flex items-center pointer-events-none text-slate-400 dark:text-slate-500">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                    </svg>
                </div>
                <input id="referral_code" type="text" name="referral_code"
                       value="{{ old('referral_code', $referralCode ?? '') }}"
                       class="form-input pl-10 rtl:pl-4 rtl:pr-10 uppercase tracking-widest" maxlength="20"
                       placeholder="{{ __('XXXXXXXX') }}">
            </div>
            @error('referral_code') <p class="form-error mt-1.5">{{ $message }}</p> @enderror
        </div>

        <!-- Submit -->
        <div class="pt-4">
            <button type="submit" class="btn-primary w-full py-3.5 justify-center shadow-glow text-sm font-bold rounded-2xl hover:scale-[1.01] transition-transform">
                {{ __('Create Account') }}
            </button>
        </div>

        <!-- Login Link -->
        <div class="text-center pt-2">
            <p class="text-xs text-slate-500 dark:text-slate-400">
                {{ __('Already have an account?') }}
                <a href="{{ route('login') }}" class="font-bold text-brand-500 hover:underline">
                    {{ __('Log in') }}
                </a>
            </p>
        </div>
    </form>
</x-guest-layout>
