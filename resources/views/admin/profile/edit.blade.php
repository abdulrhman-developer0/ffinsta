<x-admin-layout>
    <x-slot name="title">{{ __('Profile') }}</x-slot>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            {{ __('Profile') }}
        </div>
    </x-slot>

    <div class="max-w-2xl">
        <div class="card p-6">
            @if(session('success'))
                <div class="bg-green-50 text-green-700 p-4 rounded-md mb-5 text-sm dark:bg-green-900/30 dark:text-green-400">
                    {{ session('success') }}
                </div>
            @endif

            <form method="POST" action="{{ route('admin.profile.update') }}" class="space-y-5">
                @csrf
                @method('PATCH')

                <div>
                    <label for="name" class="form-label">{{ __('Name') }}</label>
                    <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" 
                           class="form-input" required>
                    @error('name') <p class="form-error">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="email" class="form-label">{{ __('Email') }}</label>
                    <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" 
                           class="form-input" required>
                    @error('email') <p class="form-error">{{ $message }}</p> @enderror
                </div>

                <div class="border-t border-slate-200 dark:border-slate-700 pt-5 mt-5">
                    <h3 class="text-sm font-semibold mb-3">{{ __('Change Password') }} <span class="text-xs text-muted font-normal">({{ __('Leave blank to keep current password') }})</span></h3>
                    
                    <div class="space-y-5">
                        <div>
                            <label for="password" class="form-label">{{ __('New Password') }}</label>
                            <input type="password" name="password" id="password" class="form-input">
                            @error('password') <p class="form-error">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="password_confirmation" class="form-label">{{ __('Confirm New Password') }}</label>
                            <input type="password" name="password_confirmation" id="password_confirmation" class="form-input">
                        </div>
                    </div>
                </div>

                <div class="pt-4 border-t border-slate-200 dark:border-slate-700 mt-5">
                    <button type="submit" class="btn-primary">{{ __('Save Changes') }}</button>
                </div>
            </form>
        </div>
    </div>
</x-admin-layout>
