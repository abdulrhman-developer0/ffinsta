<x-app-layout>
    <x-slot name="title">{{ __('My Orders') }}</x-slot>
    <x-slot name="header">{{ __('My Orders') }}</x-slot>

    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-6">
        <form method="GET" class="flex gap-2 flex-wrap">
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="{{ __('Search orders...') }}" class="form-input w-48 py-2 text-sm">
            <select name="status" class="form-input w-36 py-2 text-sm">
                <option value="">{{ __('All Statuses') }}</option>
                <option value="pending"   {{ request('status') === 'pending' ? 'selected' : '' }}>{{ __('Pending') }}</option>
                <option value="active"    {{ request('status') === 'active' ? 'selected' : '' }}>{{ __('Active') }}</option>
                <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>{{ __('Completed') }}</option>
                <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>{{ __('Cancelled') }}</option>
            </select>
            <button type="submit" class="btn-secondary btn-sm px-4">{{ __('Filter') }}</button>
        </form>
        <a href="{{ route('user.orders.create') }}" class="btn-primary btn-sm px-5">
            + {{ __('New Order') }}
        </a>
    </div>

    <div class="card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>{{ __('Order #') }}</th>
                        <th>{{ __('Account') }}</th>
                        <th>{{ __('Requested') }}</th>
                        <th>{{ __('Delivered') }}</th>
                        <th>{{ __('Progress') }}</th>
                        <th>{{ __('Status') }}</th>
                        <th>{{ __('Date') }}</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($orders as $order)
                        <tr>
                            <td class="font-mono text-xs font-semibold text-primary">{{ $order->order_number }}</td>
                            <td>{{ '@' . $order->instagram_username }}</td>
                            <td>{{ number_format($order->requested_qty) }}</td>
                            <td>{{ number_format($order->displayDeliveredQty()) }}</td>
                            <td class="min-w-[100px]">
                                <div class="progress-bar">
                                    <div class="progress-fill" style="width: {{ $order->progressPercent() }}%"></div>
                                </div>
                                <p class="text-[10px] text-muted mt-0.5">{{ $order->progressPercent() }}%</p>
                            </td>
                            <td><span class="badge-{{ $order->status }}">{{ ucfirst($order->status) }}</span></td>
                            <td class="text-muted text-xs">{{ $order->created_at->format('M d, Y') }}</td>
                            <td>
                                <a href="{{ route('user.orders.show', $order) }}"
                                   class="text-xs text-brand-500 hover:underline">{{ __('View') }}</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-12 text-muted">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-12 h-12 mx-auto mb-3 opacity-30" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                </svg>
                                <p>{{ __('No orders yet.') }}</p>
                                <a href="{{ route('user.orders.create') }}" class="text-brand-500 hover:underline text-sm mt-1 inline-block">{{ __('Create your first order') }}</a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($orders->hasPages())
            <div class="px-4 py-3" style="border-top: 1px solid var(--border-color);">
                {{ $orders->links() }}
            </div>
        @endif
    </div>
</x-app-layout>
