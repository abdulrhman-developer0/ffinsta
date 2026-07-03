<x-admin-layout>
    <x-slot name="title">{{ __('Admin Dashboard') }}</x-slot>
    <x-slot name="header">{{ __('Dashboard') }}</x-slot>

    {{-- Stats Grid --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="stat-card">
            <div class="stat-icon bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
            </div>
            <div>
                <p class="text-xs text-muted">{{ __('Total Users') }}</p>
                <p class="text-2xl font-bold text-primary">{{ number_format($stats['total_users']) }}</p>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon bg-amber-100 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
            </div>
            <div>
                <p class="text-xs text-muted">{{ __('Pending Orders') }}</p>
                <p class="text-2xl font-bold text-primary">{{ number_format($stats['pending_orders']) }}</p>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon bg-indigo-100 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                </svg>
            </div>
            <div>
                <p class="text-xs text-muted">{{ __('Active Orders') }}</p>
                <p class="text-2xl font-bold text-primary">{{ number_format($stats['active_orders']) }}</p>
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
                <p class="text-2xl font-bold text-primary">{{ number_format($stats['completed_orders']) }}</p>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon bg-cyan-100 dark:bg-cyan-900/30 text-cyan-600 dark:text-cyan-400">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                </svg>
            </div>
            <div>
                <p class="text-xs text-muted">{{ __('Total Visit') }}</p>
                <p class="text-2xl font-bold text-primary">{{ number_format($stats['total_visits']) }}</p>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon bg-fuchsia-100 dark:bg-fuchsia-900/30 text-fuchsia-600 dark:text-fuchsia-400">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
            </div>
            <div>
                <p class="text-xs text-muted">{{ __('Today Unique Visit') }}</p>
                <p class="text-2xl font-bold text-primary">{{ number_format($stats['today_visits']) }}</p>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon bg-rose-100 dark:bg-rose-900/30 text-rose-600 dark:text-rose-400">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    <circle cx="18" cy="6" r="3" fill="currentColor" class="animate-pulse" />
                </svg>
            </div>
            <div>
                <p class="text-xs text-muted">{{ __('Active Users') }}</p>
                <p class="text-2xl font-bold text-primary">{{ number_format($stats['active_users']) }}</p>
            </div>
        </div>
    </div>

    {{-- Recent Orders --}}
    <div class="card overflow-hidden">
        <div class="px-5 py-4 flex items-center justify-between" style="border-bottom: 1px solid var(--border-color);">
            <h2 class="section-title">{{ __('Recent Orders') }}</h2>
            <a href="{{ route('admin.orders.index') }}" class="text-xs text-brand-500 hover:underline">{{ __('View all') }}</a>
        </div>
        <div class="overflow-x-auto">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>{{ __('Order #') }}</th>
                        <th>{{ __('User') }}</th>
                        <th>{{ __('Account') }}</th>
                        <th>{{ __('Qty') }}</th>
                        <th>{{ __('Delivered') }}</th>
                        <th>{{ __('Status') }}</th>
                        <th>{{ __('Date') }}</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($recentOrders as $order)
                        <tr>
                            <td class="font-mono text-xs font-semibold">{{ $order->order_number }}</td>
                            <td>{{ $order->user->name }}</td>
                            <td>{{ '@' . $order->instagram_username }}</td>
                            <td>{{ number_format($order->requested_qty) }}</td>
                            <td>{{ number_format($order->delivered_qty) }}</td>
                            <td><span class="badge-{{ $order->status }}">{{ ucfirst($order->status) }}</span></td>
                            <td class="text-muted text-xs">{{ $order->created_at->format('M d, H:i') }}</td>
                            <td>
                                <a href="{{ route('admin.orders.show', $order) }}" class="text-xs text-brand-500 hover:underline">{{ __('View') }}</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</x-admin-layout>
