<x-guest-layout>
    <x-slot name="title">{{ __('Log in') }}</x-slot>

    <!-- Session Status -->
    <x-auth-session-status class="mb-5" :status="session('status')" />

    <div class="mb-6 text-center">
        <h2 class="text-xl font-extrabold text-slate-900 dark:text-white">{{ __('Welcome Back') }}</h2>
        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1.5">{{ __('Log in to your account to start growing your followers') }}</p>
    </div>

    <form method="POST" action="{{ route('login') }}" class="space-y-5">
        @csrf

        <!-- Email Address -->
        <div>
            <label for="email" class="form-label">{{ __('Email Address') }}</label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 rtl:left-auto rtl:right-0 pl-3.5 rtl:pl-0 rtl:pr-3.5 flex items-center pointer-events-none text-slate-400 dark:text-slate-500">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.206"/>
                    </svg>
                </div>
                <input id="email" type="email" name="email" value="{{ old('email') }}"
                       class="form-input pl-10 rtl:pl-4 rtl:pr-10" required autofocus autocomplete="username"
                       placeholder="you@example.com">
            </div>
            @error('email') <p class="form-error mt-1.5">{{ $message }}</p> @enderror
        </div>

        <!-- Password -->
        <div>
            <div class="flex justify-between items-center mb-1.5">
                <label for="password" class="form-label mb-0">{{ __('Password') }}</label>
                @if (Route::has('password.request'))
                    <a class="text-xs font-semibold text-brand-500 hover:underline" href="{{ route('password.request') }}">
                        {{ __('Forgot your password?') }}
                    </a>
                @endif
            </div>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 rtl:left-auto rtl:right-0 pl-3.5 rtl:pl-0 rtl:pr-3.5 flex items-center pointer-events-none text-slate-400 dark:text-slate-500">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                </div>
                <input id="password" type="password" name="password"
                       class="form-input pl-10 rtl:pl-4 rtl:pr-10"
                       required autocomplete="current-password"
                       placeholder="••••••••">
            </div>
            @error('password') <p class="form-error mt-1.5">{{ $message }}</p> @enderror
        </div>

        <!-- Remember Me -->
        <div class="flex items-center">
            <input id="remember_me" type="checkbox" name="remember" 
                   class="w-4 h-4 text-brand-500 rounded border-gray-300 dark:border-slate-800 bg-white dark:bg-slate-900 focus:ring-brand-500 cursor-pointer">
            <label for="remember_me" class="ms-2.5 text-sm font-medium text-slate-600 dark:text-slate-400 cursor-pointer select-none">{{ __('Remember me') }}</label>
        </div>

        <!-- Submit -->
        <div class="pt-2">
            <button type="submit" class="btn-primary w-full py-3.5 justify-center shadow-glow text-sm font-bold rounded-2xl hover:scale-[1.01] transition-transform">
                {{ __('Log in') }}
            </button>
        </div>

        <!-- Register Link -->
        <div class="text-center pt-2">
            <p class="text-xs text-slate-500 dark:text-slate-400">
                {{ __('Don\'t have an account?') }}
                <a href="{{ route('register') }}" class="font-bold text-brand-500 hover:underline">
                    {{ __('Create Free Account') }}
                </a>
            </p>
        </div>
    </form>
</x-guest-layout>
