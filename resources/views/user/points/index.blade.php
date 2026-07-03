<x-app-layout>
    <x-slot name="title">{{ __('Points History') }}</x-slot>
    <x-slot name="header">{{ __('Points History') }}</x-slot>

    <div class="card overflow-hidden">
        <div class="px-5 py-4 flex flex-wrap gap-2 items-center" style="border-bottom: 1px solid var(--border-color);">
            <form method="GET" class="flex gap-2 flex-wrap">
                <select name="type" class="form-input text-sm py-1.5 w-36">
                    <option value="">{{ __('All Types') }}</option>
                    <option value="earn" {{ request('type') === 'earn' ? 'selected' : '' }}>{{ __('Earned') }}</option>
                    <option value="spend" {{ request('type') === 'spend' ? 'selected' : '' }}>{{ __('Spent') }}</option>
                    <option value="coupon" {{ request('type') === 'coupon' ? 'selected' : '' }}>{{ __('Coupon') }}</option>
                    <option value="referral" {{ request('type') === 'referral' ? 'selected' : '' }}>{{ __('Referral') }}</option>
                    <option value="admin_adjustment" {{ request('type') === 'admin_adjustment' ? 'selected' : '' }}>{{ __('Admin') }}</option>
                </select>
                <input type="date" name="from" value="{{ request('from') }}" class="form-input text-sm py-1.5 w-36">
                <input type="date" name="to" value="{{ request('to') }}" class="form-input text-sm py-1.5 w-36">
                <button type="submit" class="btn-secondary btn-sm">{{ __('Filter') }}</button>
                @if(request()->hasAny(['type', 'from', 'to']))
                    <a href="{{ route('user.points.history') }}" class="btn-secondary btn-sm">{{ __('Clear') }}</a>
                @endif
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>{{ __('Description') }}</th>
                        <th>{{ __('Type') }}</th>
                        <th>{{ __('Amount') }}</th>
                        <th>{{ __('Balance After') }}</th>
                        <th>{{ __('Date') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transactions as $tx)
                        <tr>
                            <td class="max-w-xs">
                                <span class="truncate block text-sm">{{ $tx->description }}</span>
                            </td>
                            <td>
                                <span class="{{ $tx->typeBadgeClass() }} text-[11px]">{{ $tx->typeLabel() }}</span>
                            </td>
                            <td class="{{ $tx->amount > 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-500' }} font-semibold">
                                {{ $tx->amount > 0 ? '+' : '' }}{{ number_format($tx->amount) }}
                            </td>
                            <td class="font-medium text-primary">{{ number_format($tx->balance_after) }}</td>
                            <td class="text-muted text-xs">{{ $tx->created_at->format('M d, Y H:i') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-10 text-muted">{{ __('No transactions found') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($transactions->hasPages())
            <div class="px-4 py-3" style="border-top: 1px solid var(--border-color);">
                {{ $transactions->links() }}
            </div>
        @endif
    </div>
</x-app-layout>
