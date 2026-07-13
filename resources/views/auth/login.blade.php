
<x-guest-layout>
    <x-slot name="title">{{ __('Log in') }}</x-slot>

    <x-auth-session-status class="mb-5" :status="session('status')" />

    <div class="mb-6 text-center">
        <h2 class="text-xl font-extrabold text-slate-900 dark:text-white">{{ __('Welcome Back') }}</h2>
        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1.5">
            {{ __('Log in to your account to start growing your followers') }}
        </p>
    </div>

    <form method="POST" action="{{ route('login') }}" class="space-y-5">
        @csrf

        <div>
            <label for="email" class="form-label">{{ __('Email Address') }}</label>
            <div class="relative">
                <input id="email"
                       type="email"
                       name="email"
                       value="{{ old('email') }}"
                       class="form-input"
                       required
                       autofocus
                       autocomplete="username"
                       placeholder="you@example.com">
            </div>
            @error('email')
                <p class="form-error mt-1.5">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <div class="flex justify-between items-center mb-1.5">
                <label for="password" class="form-label mb-0">{{ __('Password') }}</label>

                @if (Route::has('password.request'))
                    <a class="text-xs font-semibold text-brand-500 hover:underline"
                       href="{{ route('password.request') }}">
                        {{ __('Forgot your password?') }}
                    </a>
                @endif
            </div>

            <div class="relative">
                <input id="password"
                       type="password"
                       name="password"
                       class="form-input"
                       required
                       autocomplete="current-password"
                       placeholder="••••••••">
            </div>

            @error('password')
                <p class="form-error mt-1.5">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex items-center">
            <input id="remember_me"
                   type="checkbox"
                   name="remember"
                   class="w-4 h-4 text-brand-500 rounded border-gray-300">

            <label for="remember_me"
                   class="ms-2.5 text-sm font-medium text-slate-600 dark:text-slate-400">
                {{ __('Remember me') }}
            </label>
        </div>
        
        <!-- Captcha -->
        <div class="mt-4">
            <x-turnstile-widget
                theme="auto"
                language="{{ app()->getLocale() == 'ar' ? 'ar' : 'en' }}"
            />
        
            @error('cf-turnstile-response')
                <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
            @enderror
        </div>

        <div class="pt-2">
            <button type="submit"
                    class="btn-primary w-full py-3.5 justify-center">
                {{ __('Log in') }}
            </button>
        </div>

        {{-- Google Login --}}
        <div class="relative my-4">
            <div class="absolute inset-0 flex items-center">
                <div class="w-full border-t border-slate-300"></div>
            </div>

            <div class="relative flex justify-center text-xs uppercase">
                <span class="bg-white dark:bg-slate-900 px-3 text-slate-500">
                    {{ __('Or') }}
                </span>
            </div>
        </div>

        <a href="{{ route('google.login') }}"
           class="flex w-full items-center justify-center gap-3 rounded-xl border border-slate-300 bg-white dark:bg-slate-900 px-4 py-3 font-medium hover:bg-slate-50">

            <svg xmlns="http://www.w3.org/2000/svg"
                 viewBox="0 0 48 48"
                 width="20"
                 height="20">
                <path fill="#FFC107" d="M43.6 20.5H42V20H24v8h11.3C33.7 32.7 29.3 36 24 36c-6.6 0-12-5.4-12-12S17.4 12 24 12c3 0 5.7 1.1 7.8 3l5.7-5.7C34.1 6.1 29.3 4 24 4 12.9 4 4 12.9 4 24s8.9 20 20 20 20-8.9 20-20c0-1.3-.1-2.3-.4-3.5z"/>
                <path fill="#FF3D00" d="M6.3 14.7l6.6 4.8C14.7 15 19 12 24 12c3 0 5.7 1.1 7.8 3l5.7-5.7C34.1 6.1 29.3 4 24 4c-7.7 0-14.4 4.3-17.7 10.7z"/>
                <path fill="#4CAF50" d="M24 44c5.2 0 10-2 13.6-5.2l-6.3-5.3c-2.1 1.5-4.7 2.5-7.3 2.5-5.3 0-9.7-3.3-11.3-8H6.5l-6.5 5C3.3 39.5 13 44 24 44z"/>
                <path fill="#1976D2" d="M43.6 20.5H42V20H24v8h11.3c-1.1 3.2-3.4 5.7-6.6 7.3l6.3 5.3C39.8 36.4 44 30.8 44 24c0-1.3-.1-2.3-.4-3.5z"/>
            </svg>

            <span>{{ __('Sign up with Google') }}</span>
        </a>

        <div class="text-center pt-2">
            <p class="text-xs text-slate-500 dark:text-slate-400">
                {{ __('Don\'t have an account?') }}

                <a href="{{ route('register') }}"
                   class="font-bold text-brand-500 hover:underline">
                    {{ __('Create Free Account') }}
                </a>
            </p>
        </div>
    </form>
</x-guest-layout>
