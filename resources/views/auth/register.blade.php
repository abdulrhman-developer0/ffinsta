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
        
        <!-- Captcha -->
        <div>
            <x-turnstile-widget
                theme="auto"
                language="{{ app()->getLocale() == 'ar' ? 'ar' : 'en' }}"
            />
        
            @error('cf-turnstile-response')
                <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
            @enderror
        </div>

        <!-- Submit -->
        <div class="pt-4">
            <button type="submit" class="btn-primary w-full py-3.5 justify-center shadow-glow text-sm font-bold rounded-2xl hover:scale-[1.01] transition-transform">
                {{ __('Create Account') }}
            </button>
        </div>
        
        <!-- Google Register -->
        <div class="relative my-5">
            <div class="absolute inset-0 flex items-center">
                <div class="w-full border-t border-slate-200 dark:border-slate-700"></div>
            </div>
            <div class="relative flex justify-center text-xs uppercase">
                <span class="bg-white dark:bg-slate-900 px-3 text-slate-500">
                    {{ __('Or') }}
                </span>
            </div>
        </div>

        <a href="{{ route('google.login', ['referral' => old('referral_code', $referralCode ?? '')]) }}"
           class="flex w-full items-center justify-center gap-3 rounded-2xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 px-4 py-3.5 text-sm font-semibold text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-800 transition">
        
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48" class="w-5 h-5">
                <path fill="#FFC107" d="M43.6 20.5H42V20H24v8h11.3C33.7 32.7 29.3 36 24 36c-6.6 0-12-5.4-12-12S17.4 12 24 12c3 0 5.7 1.1 7.8 3l5.7-5.7C34.1 6.1 29.3 4 24 4 12.9 4 4 12.9 4 24s8.9 20 20 20 20-8.9 20-20c0-1.3-.1-2.3-.4-3.5z"/>
                <path fill="#FF3D00" d="M6.3 14.7l6.6 4.8C14.7 15 19 12 24 12c3 0 5.7 1.1 7.8 3l5.7-5.7C34.1 6.1 29.3 4 24 4c-7.7 0-14.4 4.3-17.7 10.7z"/>
                <path fill="#4CAF50" d="M24 44c5.2 0 10-2 13.6-5.2l-6.3-5.3c-2.1 1.5-4.7 2.5-7.3 2.5-5.3 0-9.7-3.3-11.3-8H6.5l-6.5 5C3.3 39.5 13 44 24 44z"/>
                <path fill="#1976D2" d="M43.6 20.5H42V20H24v8h11.3c-1.1 3.2-3.4 5.7-6.6 7.3l6.3 5.3C39.8 36.4 44 30.8 44 24c0-1.3-.1-2.3-.4-3.5z"/>
            </svg>
        
            <span>{{ __('Sign up with Google') }}</span>
        </a>

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
