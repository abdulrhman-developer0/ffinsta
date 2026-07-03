<x-admin-layout>
    <x-slot name="title">{{ __('Orders') }}</x-slot>
    <x-slot name="header">{{ __('Order Management') }}</x-slot>

    <div class="flex flex-wrap items-center gap-2 mb-5">
        <form method="GET" class="flex flex-wrap gap-2 flex-1">
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="{{ __('Search...') }}" class="form-input w-48 py-2 text-sm">
            <select name="status" class="form-input w-36 py-2 text-sm">
                <option value="">{{ __('All Statuses') }}</option>
                <option value="pending"   {{ request('status') === 'pending' ? 'selected' : '' }}>{{ __('Pending') }}</option>
                <option value="active"    {{ request('status') === 'active' ? 'selected' : '' }}>{{ __('Active') }}</option>
                <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>{{ __('Completed') }}</option>
                <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>{{ __('Cancelled') }}</option>
            </select>
            <select name="priority" class="form-input w-32 py-2 text-sm">
                <option value="">{{ __('All Priorities') }}</option>
                <option value="normal" {{ request('priority') === 'normal' ? 'selected' : '' }}>{{ __('Normal') }}</option>
                <option value="high"   {{ request('priority') === 'high' ? 'selected' : '' }}>{{ __('High') }}</option>
            </select>
            <button type="submit" class="btn-secondary btn-sm px-4">{{ __('Filter') }}</button>
        </form>
        <a href="{{ route('admin.orders.create') }}" class="btn-primary btn-sm px-5">+ {{ __('Manual Order') }}</a>
    </div>

    <div class="card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>{{ __('Order #') }}</th>
                        <th>{{ __('User') }}</th>
                        <th>{{ __('Account') }}</th>
                        <th>{{ __('Progress') }}</th>
                        <th>{{ __('Priority') }}</th>
                        <th>{{ __('Status') }}</th>
                        <th>{{ __('Date') }}</th>
                        <th>{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($orders as $order)
                        <tr>
                            <td class="font-mono text-xs font-semibold text-primary">{{ $order->order_number }}</td>
                            <td>
                                <a href="{{ route('admin.users.show', $order->user) }}" class="text-brand-500 hover:underline text-sm">
                                    {{ $order->user->name }}
                                </a>
                            </td>
                            <td>{{ '@' . $order->instagram_username }}</td>
                            <td>
                                <div class="progress-bar w-20">
                                    <div class="progress-fill" style="width: {{ $order->progressPercent() }}%"></div>
                                </div>
                                <p class="text-[10px] text-muted mt-0.5">{{ $order->delivered_qty }}/{{ $order->requested_qty }}</p>
                            </td>
                            <td><span class="badge-{{ $order->priority }}">{{ ucfirst($order->priority) }}</span></td>
                            <td><span class="badge-{{ $order->status }}">{{ ucfirst($order->status) }}</span></td>
                            <td class="text-muted text-xs">{{ $order->created_at->format('M d') }}</td>
                            <td>
                                <div class="flex items-center gap-1.5 flex-wrap">
                                    <a href="{{ route('admin.orders.show', $order) }}" class="btn-secondary btn-sm">{{ __('View') }}</a>
                                    @if($order->status === 'pending')
                                        <form method="POST" action="{{ route('admin.orders.activate', $order) }}">
                                            @csrf
                                            <button type="submit" class="btn-success btn-sm">{{ __('Activate') }}</button>
                                        </form>
                                    @endif
                                    @if(!in_array($order->status, ['completed', 'cancelled']))
                                        <form method="POST" action="{{ route('admin.orders.update', $order) }}">
                                            @csrf @method('PATCH')
                                            <input type="hidden" name="status" value="completed">
                                            <button type="submit" class="btn-success btn-sm"
                                                    onclick="return confirm('{{ __('Mark this order as completed?') }}')">
                                                {{ __('Complete') }}
                                            </button>
                                        </form>
                                        <form method="POST" action="{{ route('admin.orders.cancel', $order) }}">
                                            @csrf
                                            <button type="submit" class="btn-danger btn-sm"
                                                    onclick="return confirm('{{ __('Cancel this order?') }}')">
                                                {{ __('Cancel') }}
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-10 text-muted">{{ __('No orders found') }}</td>
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
</x-admin-layout>
