<x-app-layout>
    <x-slot name="title">{{ __('Profile') }}</x-slot>
    <x-slot name="header">{{ __('Profile Settings') }}</x-slot>

    <div class="max-w-lg mx-auto space-y-6">
        {{-- Profile Info --}}
        <div class="card p-6">
            <h2 class="section-title mb-4">{{ __('Account Information') }}</h2>
            <form method="POST" action="{{ route('user.profile.update') }}" class="space-y-4">
                @csrf @method('PATCH')

                <div>
                    <label class="form-label" for="name">{{ __('Full Name') }}</label>
                    <input type="text" id="name" name="name" value="{{ old('name', auth()->user()->name) }}"
                           class="form-input" required maxlength="255">
                    @error('name') <p class="form-error">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="form-label" for="email">{{ __('Email Address') }}</label>
                    <input type="email" id="email" name="email" value="{{ old('email', auth()->user()->email) }}"
                           class="form-input" required maxlength="255">
                    @error('email') <p class="form-error">{{ $message }}</p> @enderror
                </div>

                <div class="pt-1">
                    <button type="submit" class="btn-primary">{{ __('Save Changes') }}</button>
                </div>
            </form>
        </div>

        {{-- Change Password --}}
        <div class="card p-6">
            <h2 class="section-title mb-4">{{ __('Change Password') }}</h2>
            <form method="POST" action="{{ route('user.profile.password') }}" class="space-y-4">
                @csrf @method('PATCH')

                <div>
                    <label class="form-label" for="current_password">{{ __('Current Password') }}</label>
                    <input type="password" id="current_password" name="current_password" class="form-input" required>
                    @error('current_password') <p class="form-error">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="form-label" for="password">{{ __('New Password') }}</label>
                    <input type="password" id="password" name="password" class="form-input" required>
                    @error('password') <p class="form-error">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="form-label" for="password_confirmation">{{ __('Confirm New Password') }}</label>
                    <input type="password" id="password_confirmation" name="password_confirmation" class="form-input" required>
                </div>

                <div class="pt-1">
                    <button type="submit" class="btn-secondary">{{ __('Update Password') }}</button>
                </div>
            </form>
        </div>

        {{-- Account Info --}}
        <div class="card p-6">
            <h2 class="section-title mb-4">{{ __('Account Details') }}</h2>
            <div class="space-y-3 text-sm">
                <div class="flex justify-between">
                    <span class="text-muted">{{ __('Joined') }}</span>
                    <span class="font-medium text-primary">{{ auth()->user()->created_at->format('M d, Y') }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-muted">{{ __('Referral Code') }}</span>
                    <span class="font-mono font-semibold text-brand-500">{{ auth()->user()->referral_code }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-muted">{{ __('Points Balance') }}</span>
                    <span class="font-bold text-primary">{{ number_format(auth()->user()->points) }}</span>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
