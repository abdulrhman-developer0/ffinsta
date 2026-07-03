<x-app-layout>
    <x-slot name="title">{{ __('Edit Instagram Account') }}</x-slot>
    <x-slot name="header">{{ __('Edit Account') }}</x-slot>

    <div class="max-w-md mx-auto">
        <div class="card p-6">
            <form method="POST" action="{{ route('user.instagram.update', $account) }}" class="space-y-4">
                @csrf @method('PATCH')

                <div>
                    <label class="form-label" for="username">{{ __('Instagram Username') }}</label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-muted text-sm">@</span>
                        <input type="text" id="username" name="username"
                               value="{{ old('username', $account->username) }}"
                               class="form-input pl-7" maxlength="30" required
                               pattern="[a-zA-Z0-9._]{1,30}">
                    </div>
                    @error('username') <p class="form-error">{{ $message }}</p> @enderror
                </div>

                <div class="flex gap-3 pt-2">
                    <a href="{{ route('user.instagram.index') }}" class="btn-secondary flex-1">{{ __('Cancel') }}</a>
                    <button type="submit" class="btn-primary flex-1">{{ __('Save Changes') }}</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
