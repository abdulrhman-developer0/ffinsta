<x-app-layout>
    <x-slot name="title">{{ __('Dashboard') }}</x-slot>
    <x-slot name="header">{{ __('Dashboard') }}</x-slot>

    {{-- Stats Grid --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        {{-- Points Balance --}}
        <div class="relative stat-card col-span-2 lg:col-span-1 bg-gradient-to-br from-brand-500 to-brand-700 text-white border-0 hover:shadow-lg transition transform hover:-translate-y-1 group">
            <a href="{{ route('user.purchase-points.index') }}" class="absolute inset-0 z-10" title="{{ __('Buy Points') }}"></a>
            <div class="stat-icon bg-white/20">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <p class="text-sm opacity-80">{{ __('Points Balance') }}</p>
                <div class="flex items-center gap-2">
                    <p class="text-2xl font-bold">{{ number_format($stats['points']) }}</p>
                    <div class="bg-white/20 rounded-full w-5 h-5 flex items-center justify-center group-hover:bg-white/30 transition shadow-sm text-lg font-medium leading-none pb-0.5">
                        +
                    </div>
                </div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                </svg>
            </div>
            <div>
                <p class="text-xs text-muted">{{ __('Active Orders') }}</p>
                <p class="text-2xl font-bold text-primary">{{ $stats['active_orders'] }}</p>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <p class="text-xs text-muted">{{ __('Completed') }}</p>
                <p class="text-2xl font-bold text-primary">{{ $stats['completed_orders'] }}</p>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon bg-purple-100 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
            </div>
            <div>
                <p class="text-xs text-muted">{{ __('Followers Gotten') }}</p>
                <p class="text-2xl font-bold text-primary">{{ number_format($stats['total_delivered']) }}</p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Quick Actions --}}
        <div class="card p-5">
            <h2 class="section-title mb-4">{{ __('Quick Actions') }}</h2>
            <div class="space-y-2.5">
                <a href="{{ route('user.orders.create') }}" class="btn-primary w-full">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    {{ __('Create Order') }}
                </a>
                <a href="{{ route('user.earn.index') }}" class="btn-secondary w-full">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    {{ __('Earn Points') }}
                </a>
                {{-- Redeem Coupon --}}
                <form action="{{ route('user.coupons.redeem') }}" method="POST" class="flex gap-2">
                    @csrf
                    <input type="text" name="code" placeholder="{{ __('Coupon code') }}"
                           class="form-input flex-1 py-2 text-xs uppercase tracking-wider" maxlength="50">
                    <button type="submit" class="btn-success btn-sm px-4">{{ __('Redeem') }}</button>
                </form>
                @error('code') <p class="form-error">{{ $message }}</p> @enderror
            </div>
        </div>

        {{-- Active Orders --}}
        <div class="card p-5 col-span-1 lg:col-span-2">
            <div class="flex items-center justify-between mb-4">
                <h2 class="section-title">{{ __('Active Orders') }}</h2>
                <a href="{{ route('user.orders.index') }}" class="text-xs text-brand-500 hover:underline">{{ __('View all') }}</a>
            </div>
            @forelse($activeOrders as $order)
                <div class="flex items-center gap-3 py-2.5" style="border-bottom: 1px solid var(--border-subtle);">
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 mb-1">
                            <span class="text-sm font-medium text-primary">{{ $order->order_number }}</span>
                            <span class="badge-{{ $order->status }}">{{ ucfirst($order->status) }}</span>
                        </div>
                        <p class="text-xs text-muted">{{ '@' . $order->instagram_username }}</p>
                        <div class="mt-1.5 progress-bar">
                            <div class="progress-fill" style="width: {{ $order->progressPercent() }}%"></div>
                        </div>
                        <p class="text-[10px] text-muted mt-1">{{ $order->displayDeliveredQty() }} / {{ $order->requested_qty }} {{ __('followers') }}</p>
                    </div>
                </div>
            @empty
                <p class="text-sm text-muted text-center py-6">{{ __('No active orders. Create one to get started!') }}</p>
            @endforelse
        </div>
    </div>

    {{-- Recent Points Activity --}}
    <div class="card mt-6">
        <div class="flex items-center justify-between px-5 py-4" style="border-bottom: 1px solid var(--border-color);">
            <h2 class="section-title">{{ __('Recent Points Activity') }}</h2>
            <a href="{{ route('user.points.history') }}" class="text-xs text-brand-500 hover:underline">{{ __('View all') }}</a>
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
                    @forelse($recentTransactions as $tx)
                        <tr>
                            <td class="max-w-xs">
                                <span class="truncate block">{{ $tx->description }}</span>
                            </td>
                            <td>
                                <span class="{{ $tx->typeBadgeClass() }}">{{ $tx->typeLabel() }}</span>
                            </td>
                            <td class="{{ $tx->amount > 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-500' }} font-semibold">
                                {{ $tx->amount > 0 ? '+' : '' }}{{ number_format($tx->amount) }}
                            </td>
                            <td class="font-medium">{{ number_format($tx->balance_after) }}</td>
                            <td class="text-muted">{{ $tx->created_at->format('M d, H:i') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-8 text-muted">{{ __('No transactions yet') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
