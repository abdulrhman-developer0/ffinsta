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
                                <p class="text-[10px] text-muted mt-0.5">{{ $order->displayDeliveredQty() }}/{{ $order->requested_qty }}</p>
                            </td>
                            <td>
                                <form method="POST" action="{{ route('admin.orders.toggle-priority', $order) }}">
                                    @csrf
                                    <button type="submit" class="focus:outline-none flex items-center justify-center p-1 rounded hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors" title="{{ __('Toggle Priority') }}">
                                        @if($order->priority === 'high')
                                            <!-- Filled Star for High Priority -->
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-yellow-400 drop-shadow-sm" viewBox="0 0 20 20" fill="currentColor">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                            </svg>
                                        @else
                                            <!-- Outline Star for Normal Priority -->
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-slate-300 dark:text-slate-600 hover:text-yellow-400 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                                            </svg>
                                        @endif
                                    </button>
                                </form>
                            </td>
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
