<x-admin-layout>
    <x-slot name="title">{{ __('Coupons') }}</x-slot>
    <x-slot name="header">{{ __('Coupon Management') }}</x-slot>

    <div class="flex justify-end mb-5">
        <a href="{{ route('admin.coupons.create') }}" class="btn-primary btn-sm px-5">+ {{ __('New Coupon') }}</a>
    </div>

    <div class="card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>{{ __('Code') }}</th>
                        <th>{{ __('Points') }}</th>
                        <th>{{ __('Usage') }}</th>
                        <th>{{ __('Expires') }}</th>
                        <th>{{ __('Status') }}</th>
                        <th>{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($coupons as $coupon)
                        <tr>
                            <td class="font-mono font-bold text-primary tracking-wider">{{ $coupon->code }}</td>
                            <td class="text-emerald-600 dark:text-emerald-400 font-semibold">+{{ $coupon->reward_points }}</td>
                            <td class="text-muted">
                                {{ $coupon->used_count }}
                                @if($coupon->usage_limit > 0)
                                    / {{ $coupon->usage_limit }}
                                @else
                                    / ∞
                                @endif
                            </td>
                            <td class="text-muted text-xs">{{ $coupon->expires_at?->format('M d, Y') ?? '—' }}</td>
                            <td>
                                @if($coupon->status === 'active')
                                    <span class="badge-completed">{{ __('Active') }}</span>
                                @else
                                    <span class="badge-cancelled">{{ __('Inactive') }}</span>
                                @endif
                            </td>
                            <td>
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('admin.coupons.edit', $coupon) }}" class="btn-secondary btn-sm">{{ __('Edit') }}</a>
                                    <form method="POST" action="{{ route('admin.coupons.destroy', $coupon) }}"
                                          onsubmit="return confirm('{{ __('Delete this coupon?') }}')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn-danger btn-sm">{{ __('Delete') }}</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-10 text-muted">{{ __('No coupons yet') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($coupons->hasPages())
            <div class="px-4 py-3" style="border-top: 1px solid var(--border-color);">
                {{ $coupons->links() }}
            </div>
        @endif
    </div>
</x-admin-layout>
