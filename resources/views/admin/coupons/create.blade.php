<x-admin-layout>
    <x-slot name="title">{{ __('Create Coupon') }}</x-slot>
    <x-slot name="header">{{ __('Create Coupon') }}</x-slot>

    <div class="max-w-md mx-auto">
        <div class="card p-6">
            <form method="POST" action="{{ route('admin.coupons.store') }}" class="space-y-4">
                @csrf

                <div>
                    <label class="form-label">{{ __('Coupon Code') }}</label>
                    <input type="text" name="code" value="{{ old('code') }}"
                           class="form-input uppercase tracking-widest" maxlength="50" required placeholder="SUMMER50">
                    @error('code') <p class="form-error">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="form-label">{{ __('Reward Points') }}</label>
                    <input type="number" name="reward_points" value="{{ old('reward_points', 100) }}"
                           class="form-input" min="1" required>
                    @error('reward_points') <p class="form-error">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="form-label">{{ __('Usage Limit') }} <span class="text-muted text-xs">({{ __('0 = unlimited') }})</span></label>
                    <input type="number" name="usage_limit" value="{{ old('usage_limit', 0) }}"
                           class="form-input" min="0">
                    @error('usage_limit') <p class="form-error">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="form-label">{{ __('Expiry Date') }} <span class="text-muted text-xs">({{ __('optional') }})</span></label>
                    <input type="date" name="expires_at" value="{{ old('expires_at') }}" class="form-input">
                    @error('expires_at') <p class="form-error">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="form-label">{{ __('Status') }}</label>
                    <select name="status" class="form-input">
                        <option value="active">{{ __('Active') }}</option>
                        <option value="inactive">{{ __('Inactive') }}</option>
                    </select>
                </div>

                <div class="flex gap-3 pt-2">
                    <a href="{{ route('admin.coupons.index') }}" class="btn-secondary flex-1">{{ __('Cancel') }}</a>
                    <button type="submit" class="btn-primary flex-1">{{ __('Create Coupon') }}</button>
                </div>
            </form>
        </div>
    </div>
</x-admin-layout>
