<x-admin-layout>
    <x-slot name="title">{{ __('Edit Coupon') }}</x-slot>
    <x-slot name="header">{{ __('Edit Coupon') }}: {{ $coupon->code }}</x-slot>

    <div class="max-w-md mx-auto">
        <div class="card p-6">
            <form method="POST" action="{{ route('admin.coupons.update', $coupon) }}" class="space-y-4">
                @csrf @method('PUT')

                <div>
                    <label class="form-label">{{ __('Coupon Code') }}</label>
                    <input type="text" class="form-input opacity-60 cursor-not-allowed" value="{{ $coupon->code }}" disabled>
                    <p class="text-xs text-muted mt-1">{{ __('Code cannot be changed after creation') }}</p>
                </div>

                <div>
                    <label class="form-label">{{ __('Reward Points') }}</label>
                    <input type="number" name="reward_points" value="{{ old('reward_points', $coupon->reward_points) }}"
                           class="form-input" min="1" required>
                    @error('reward_points') <p class="form-error">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="form-label">{{ __('Usage Limit') }} <span class="text-muted text-xs">({{ __('0 = unlimited') }})</span></label>
                    <input type="number" name="usage_limit" value="{{ old('usage_limit', $coupon->usage_limit) }}"
                           class="form-input" min="0">
                </div>

                <div>
                    <label class="form-label">{{ __('Expiry Date') }}</label>
                    <input type="date" name="expires_at" value="{{ old('expires_at', $coupon->expires_at?->format('Y-m-d')) }}" class="form-input">
                </div>

                <div>
                    <label class="form-label">{{ __('Status') }}</label>
                    <select name="status" class="form-input">
                        <option value="active"   {{ $coupon->status === 'active' ? 'selected' : '' }}>{{ __('Active') }}</option>
                        <option value="inactive" {{ $coupon->status === 'inactive' ? 'selected' : '' }}>{{ __('Inactive') }}</option>
                    </select>
                </div>

                <div class="flex gap-3 pt-2">
                    <a href="{{ route('admin.coupons.index') }}" class="btn-secondary flex-1">{{ __('Cancel') }}</a>
                    <button type="submit" class="btn-primary flex-1">{{ __('Save Changes') }}</button>
                </div>
            </form>
        </div>
    </div>
</x-admin-layout>
