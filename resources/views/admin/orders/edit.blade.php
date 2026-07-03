<x-admin-layout>
    <x-slot name="title">{{ __('Edit Order') }}</x-slot>
    <x-slot name="header">{{ __('Edit Order') }}: {{ $order->order_number }}</x-slot>

    <div class="max-w-lg mx-auto">
        <div class="card p-6">
            <form method="POST" action="{{ route('admin.orders.update', $order) }}" class="space-y-4">
                @csrf @method('PATCH')

                <div>
                    <label class="form-label">{{ __('Order Number') }}</label>
                    <input type="text" class="form-input opacity-60 cursor-not-allowed" value="{{ $order->order_number }}" disabled>
                </div>

                <div>
                    <label class="form-label">{{ __('Instagram Username') }}</label>
                    <input type="text" class="form-input opacity-60 cursor-not-allowed" value="{{ '@' . $order->instagram_username }}" disabled>
                </div>

                <div>
                    <label class="form-label">{{ __('Delivered Qty') }}</label>
                    <input type="number" name="delivered_qty"
                           value="{{ old('delivered_qty', $order->delivered_qty) }}"
                           class="form-input" min="0" max="{{ $order->requested_qty }}" required>
                    <p class="text-xs text-muted mt-1">{{ __('Max') }}: {{ $order->requested_qty }}</p>
                    @error('delivered_qty') <p class="form-error">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="form-label">{{ __('Status') }}</label>
                    <select name="status" class="form-input" required>
                        @foreach(['pending', 'active', 'completed', 'cancelled'] as $s)
                            <option value="{{ $s }}" {{ $order->status === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                        @endforeach
                    </select>
                    @error('status') <p class="form-error">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="form-label">{{ __('Priority') }}</label>
                    <select name="priority" class="form-input">
                        <option value="normal" {{ $order->priority === 'normal' ? 'selected' : '' }}>{{ __('Normal') }}</option>
                        <option value="high"   {{ $order->priority === 'high' ? 'selected' : '' }}>{{ __('High') }}</option>
                    </select>
                </div>

                <div>
                    <label class="form-label">{{ __('Admin Notes') }}</label>
                    <textarea name="admin_notes" class="form-input" rows="3">{{ old('admin_notes', $order->admin_notes) }}</textarea>
                </div>

                <div class="flex gap-3 pt-2">
                    <a href="{{ route('admin.orders.show', $order) }}" class="btn-secondary flex-1">{{ __('Cancel') }}</a>
                    <button type="submit" class="btn-primary flex-1">{{ __('Save Changes') }}</button>
                </div>
            </form>
        </div>
    </div>
</x-admin-layout>
