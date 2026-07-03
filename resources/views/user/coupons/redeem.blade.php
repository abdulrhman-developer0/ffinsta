<x-app-layout>
    <x-slot name="title">{{ __('Redeem Coupon') }}</x-slot>
    <x-slot name="header">{{ __('Coupons') }}</x-slot>

    <div class="max-w-lg mx-auto space-y-6">
        {{-- Redeem Form --}}
        <div class="card p-6">
            <h2 class="section-title mb-4">{{ __('Redeem a Coupon') }}</h2>
            <form method="POST" action="{{ route('user.coupons.redeem') }}" class="flex gap-3">
                @csrf
                <input type="text" name="code" value="{{ old('code') }}"
                       class="form-input flex-1 uppercase tracking-widest"
                       placeholder="{{ __('Enter coupon code') }}" maxlength="50" required>
                <button type="submit" class="btn-success px-6">{{ __('Redeem') }}</button>
            </form>
            @error('code') <p class="form-error mt-2">{{ $message }}</p> @enderror
        </div>

        {{-- Past Redemptions --}}
        @if($redemptions->isNotEmpty())
            <div class="card overflow-hidden">
                <div class="px-5 py-3" style="border-bottom: 1px solid var(--border-color);">
                    <h3 class="section-title">{{ __('Redemption History') }}</h3>
                </div>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>{{ __('Code') }}</th>
                            <th>{{ __('Points') }}</th>
                            <th>{{ __('Date') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($redemptions as $r)
                            <tr>
                                <td class="font-mono font-semibold">{{ $r->coupon->code }}</td>
                                <td class="text-emerald-600 dark:text-emerald-400 font-bold">+{{ $r->points_awarded }}</td>
                                <td class="text-muted text-xs">{{ $r->created_at->format('M d, Y') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</x-app-layout>
