<x-admin-layout>
    <x-slot name="title">{{ __('Instagram Account') }}</x-slot>
    <x-slot name="header">{{ __('Instagram Account Details') }}</x-slot>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="col-span-1 space-y-6">
            <div class="card p-6">
                <div class="flex items-center gap-4 mb-6">
                    <div class="w-16 h-16 rounded-full bg-primary/10 flex items-center justify-center text-primary text-2xl font-bold">
                        {{ strtoupper(substr($account->username, 0, 1)) }}
                    </div>
                    <div>
                        <h2 class="text-xl font-bold">{{ '@' . $account->username }}</h2>
                        <span class="badge-{{ $account->status === 'active' ? 'active' : 'cancelled' }} mt-1">
                            {{ ucfirst($account->status) }}
                        </span>
                    </div>
                </div>

                <div class="space-y-4">
                    <div class="flex justify-between pb-3" style="border-bottom: 1px solid var(--border-color);">
                        <span class="text-muted">{{ __('Owner') }}</span>
                        <a href="{{ route('admin.users.show', $account->user) }}" class="text-brand-500 font-medium hover:underline">{{ $account->user->name }}</a>
                    </div>
                    
                    <div class="flex justify-between pb-3" style="border-bottom: 1px solid var(--border-color);">
                        <span class="text-muted">{{ __('Added On') }}</span>
                        <span class="font-medium">{{ $account->created_at->format('M d, Y') }}</span>
                    </div>

                    <div class="flex justify-between pb-3" style="border-bottom: 1px solid var(--border-color);">
                        <span class="text-muted">{{ __('Verified') }}</span>
                        <span class="font-medium">{{ $account->is_verified ? __('Yes') : __('No') }}</span>
                    </div>
                </div>

                <div class="mt-6">
                    <form method="POST" action="{{ route('admin.instagram.status', $account) }}">
                        @csrf @method('PATCH')
                        <button type="submit" class="btn-{{ $account->status === 'active' ? 'danger' : 'primary' }} w-full">
                            {{ $account->status === 'active' ? __('Suspend Account') : __('Activate Account') }}
                        </button>
                    </form>
                </div>
            </div>

            <!-- Connection Details Card -->
            <div class="card p-6">
                <h3 class="font-semibold text-lg mb-4">{{ __('Connection Details (API/OAuth)') }}</h3>
                <div class="space-y-4 text-sm">
                    <div>
                        <span class="text-muted block text-xs uppercase tracking-wider">{{ __('Instagram User ID') }}</span>
                        <span class="font-mono text-gray-800 dark:text-gray-200 break-all">{{ $account->instagram_user_id ?: __('Not Available') }}</span>
                    </div>

                    <div>
                        <span class="text-muted block text-xs uppercase tracking-wider">{{ __('OAuth Access Token') }}</span>
                        @if($account->oauth_access_token)
                            <div x-data="{ showToken: false }" class="mt-1">
                                <div x-show="!showToken" class="font-mono text-gray-800 dark:text-gray-200 break-all bg-gray-50 dark:bg-gray-800 p-2 rounded border border-gray-200 dark:border-gray-700">
                                    {{ Str::limit($account->oauth_access_token, 10) }}••••••••••••••••••••
                                    <button @click="showToken = true" type="button" class="text-xs text-brand-500 hover:underline ml-2">{{ __('Show') }}</button>
                                </div>
                                <div x-show="showToken" class="font-mono text-gray-800 dark:text-gray-200 break-all bg-gray-50 dark:bg-gray-800 p-2 rounded border border-gray-200 dark:border-gray-700">
                                    {{ $account->oauth_access_token }}
                                    <button @click="showToken = false" type="button" class="text-xs text-brand-500 hover:underline ml-2">{{ __('Hide') }}</button>
                                </div>
                            </div>
                        @else
                            <span class="text-gray-500">{{ __('No OAuth token') }}</span>
                        @endif
                    </div>

                    @if($account->oauth_expires_at)
                    <div>
                        <span class="text-muted block text-xs uppercase tracking-wider">{{ __('Token Expires At') }}</span>
                        <span class="text-gray-800 dark:text-gray-200">{{ $account->oauth_expires_at->format('M d, Y H:i:s') }}</span>
                    </div>
                    @endif

                    @if($account->cookies)
                    <div>
                        <span class="text-muted block text-xs uppercase tracking-wider">{{ __('Session / Cookies') }}</span>
                        <div x-data="{ showCookies: false }" class="mt-1">
                            <div x-show="!showCookies">
                                <span class="text-gray-500">{{ __('Cookies are stored securely.') }}</span>
                                <button @click="showCookies = true" type="button" class="text-xs text-brand-500 hover:underline ml-2">{{ __('View') }}</button>
                            </div>
                            <div x-show="showCookies" class="font-mono text-xs text-gray-800 dark:text-gray-200 break-all bg-gray-50 dark:bg-gray-800 p-2 rounded border border-gray-200 dark:border-gray-700 max-h-32 overflow-y-auto">
                                {{ $account->cookies }}
                                <div class="mt-2 text-right">
                                    <button @click="showCookies = false" type="button" class="text-brand-500 hover:underline">{{ __('Hide') }}</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-span-1 md:col-span-2">
            <div class="card">
                <div class="p-5" style="border-bottom: 1px solid var(--border-color);">
                    <h3 class="font-semibold text-lg">{{ __('Associated Orders') }}</h3>
                </div>
                <div class="p-0">
                    @if($account->orders->count() > 0)
                        <table class="data-table w-full">
                            <thead>
                                <tr>
                                    <th>{{ __('Order #') }}</th>
                                    <th>{{ __('Qty') }}</th>
                                    <th>{{ __('Status') }}</th>
                                    <th>{{ __('Date') }}</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($account->orders as $order)
                                    <tr>
                                        <td class="font-mono text-sm">{{ $order->order_number }}</td>
                                        <td>{{ number_format($order->requested_qty) }}</td>
                                        <td><span class="badge-{{ $order->status }}">{{ ucfirst($order->status) }}</span></td>
                                        <td class="text-muted text-sm">{{ $order->created_at->format('M d, Y') }}</td>
                                        <td>
                                            <a href="{{ route('admin.orders.show', $order) }}" class="text-xs text-brand-500 hover:underline">{{ __('View') }}</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <div class="p-8 text-center text-muted">
                            {{ __('No orders found for this account.') }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>
