<x-admin-layout>
    <x-slot name="title">{{ $user->name }}</x-slot>
    <x-slot name="header">{{ __('User Details') }}</x-slot>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- User Info --}}
        <div class="space-y-5">
            <div class="card p-5">
                <div class="flex items-center gap-4 mb-4">
                    <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-brand-400 to-brand-600 flex items-center justify-center text-white font-bold text-xl">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </div>
                    <div>
                        <h2 class="font-bold text-primary">{{ $user->name }}</h2>
                        <p class="text-sm text-muted">{{ $user->email }}</p>
                        @if($user->is_suspended)
                            <span class="badge badge-cancelled mt-1">{{ __('Suspended') }}</span>
                        @else
                            <span class="badge badge-completed mt-1">{{ __('Active') }}</span>
                        @endif
                    </div>
                </div>

                <div class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="text-muted">{{ __('Points') }}</span>
                        <span class="font-bold text-brand-500">{{ number_format($user->points) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-muted">{{ __('Referral Code') }}</span>
                        <span class="font-mono font-semibold text-primary">{{ $user->referral_code }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-muted">{{ __('Joined') }}</span>
                        <span class="text-primary">{{ $user->created_at->format('M d, Y') }}</span>
                    </div>
                </div>

                <div class="divider my-4"></div>

                <div class="flex flex-col gap-2">
                    <form method="POST" action="{{ route('admin.users.impersonate', $user) }}" class="w-full">
                        @csrf
                        <button type="submit" class="btn-primary btn-sm w-full font-bold flex justify-center items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" /></svg>
                            {{ __('Login as User') }}
                        </button>
                    </form>
                    <div class="flex gap-2">
                        <form method="POST" action="{{ route('admin.users.suspend', $user) }}" class="flex-1">
                            @csrf @method('PATCH')
                            <button type="submit" class="{{ $user->is_suspended ? 'btn-success' : 'btn-danger' }} btn-sm w-full">
                                {{ $user->is_suspended ? __('Unsuspend') : __('Suspend') }}
                            </button>
                        </form>
                        <form method="POST" action="{{ route('admin.users.destroy', $user) }}"
                              onsubmit="return confirm('{{ __('Delete this user permanently?') }}')" class="flex-1">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn-danger btn-sm w-full">{{ __('Delete') }}</button>
                        </form>
                    </div>
                </div>
            </div>

            {{-- Adjust Points --}}
            <div class="card p-5">
                <h3 class="section-title mb-3">{{ __('Adjust Points') }}</h3>
                <form method="POST" action="{{ route('admin.users.adjust-points', $user) }}" class="space-y-3">
                    @csrf
                    <div>
                        <label class="form-label">{{ __('Action') }}</label>
                        <select name="action" class="form-input" required>
                            <option value="add">{{ __('Add Points (+)') }}</option>
                            <option value="remove">{{ __('Remove Points (-)') }}</option>
                        </select>
                    </div>
                    <div>
                        <label class="form-label">{{ __('Amount') }}</label>
                        <input type="number" name="amount" class="form-input" placeholder="e.g. 100" min="1" required>
                    </div>
                    <div>
                        <label class="form-label">{{ __('Reason') }}</label>
                        <input type="text" name="description" class="form-input" placeholder="{{ __('e.g. Compensation') }}" required>
                    </div>
                    <button type="submit" class="btn-secondary w-full">{{ __('Apply Adjustment') }}</button>
                </form>
            </div>
        </div>

        {{-- User Orders --}}
        <div class="lg:col-span-2">
            <div class="card overflow-hidden">
                <div class="px-5 py-3" style="border-bottom: 1px solid var(--border-color);">
                    <h3 class="section-title">{{ __('Orders') }}</h3>
                </div>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>{{ __('Order #') }}</th>
                            <th>{{ __('Account') }}</th>
                            <th>{{ __('Progress') }}</th>
                            <th>{{ __('Status') }}</th>
                            <th>{{ __('Date') }}</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($user->orders->take(20) as $order)
                            <tr>
                                <td class="font-mono text-xs font-semibold">{{ $order->order_number }}</td>
                                <td>{{ '@' . $order->instagram_username }}</td>
                                <td>{{ $order->delivered_qty }}/{{ $order->requested_qty }}</td>
                                <td><span class="badge-{{ $order->status }}">{{ ucfirst($order->status) }}</span></td>
                                <td class="text-muted text-xs">{{ $order->created_at->format('M d') }}</td>
                                <td>
                                    <a href="{{ route('admin.orders.show', $order) }}" class="text-xs text-brand-500 hover:underline">{{ __('View') }}</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-8 text-muted">{{ __('No orders') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-admin-layout>
