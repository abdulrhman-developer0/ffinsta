<x-guest-layout>
    <div class="mb-4 text-sm text-gray-600">
        {{ __('Forgot your password? No problem. Just let us know your email address and we will email you a password reset link that will allow you to choose a new one.') }}
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
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

        <div class="pt-4">
            <button type="submit" class="btn-primary w-full py-3.5 justify-center shadow-glow text-sm font-bold rounded-2xl hover:scale-[1.01] transition-transform">
                {{ __('Email Password Reset Link') }}
            </button>
        </div>
        
    </form>
</x-guest-layout>
