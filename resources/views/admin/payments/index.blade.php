<x-admin-layout>
    <x-slot name="title">{{ __('Payments Logs') }}</x-slot>
    <x-slot name="header">{{ __('Payments Logs') }}</x-slot>

    {{-- Filter Bar --}}
    <div class="card p-4 mb-6">
        <form method="GET" action="{{ route('admin.payments.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
            <div>
                <label class="form-label text-xs font-semibold mb-1">{{ __('Search') }}</label>
                <input type="text" name="search" value="{{ request('search') }}" class="form-input text-xs" placeholder="{{ __('Email, Phone, Transaction ID...') }}">
            </div>
            <div>
                <label class="form-label text-xs font-semibold mb-1">{{ __('Payment Method') }}</label>
                <select name="method" class="form-input text-xs">
                    <option value="">{{ __('All Methods') }}</option>
                    <option value="vodafone_cash" {{ request('method') === 'vodafone_cash' ? 'selected' : '' }}>{{ __('Vodafone Cash') }}</option>
                    <option value="binance_pay" {{ request('method') === 'binance_pay' ? 'selected' : '' }}>{{ __('Binance Pay') }}</option>
                </select>
            </div>
            <div>
                <label class="form-label text-xs font-semibold mb-1">{{ __('Status') }}</label>
                <select name="status" class="form-input text-xs">
                    <option value="">{{ __('All Statuses') }}</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>{{ __('Pending') }}</option>
                    <option value="success" {{ request('status') === 'success' ? 'selected' : '' }}>{{ __('Success') }}</option>
                    <option value="failed" {{ request('status') === 'failed' ? 'selected' : '' }}>{{ __('Failed') }}</option>
                </select>
            </div>
            <div class="flex gap-2">
                <button type="submit" class="btn-primary btn-sm flex-1">{{ __('Filter') }}</button>
                <a href="{{ route('admin.payments.index') }}" class="btn-secondary btn-sm flex-1 text-center flex items-center justify-center">{{ __('Clear') }}</a>
            </div>
        </form>
    </div>

    {{-- Log Table --}}
    <div class="card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="data-table text-sm">
                <thead>
                    <tr>
                        <th>{{ __('User') }}</th>
                        <th>{{ __('Method') }}</th>
                        <th>{{ __('Amount') }}</th>
                        <th>{{ __('Points') }}</th>
                        <th>{{ __('Details') }}</th>
                        <th>{{ __('Status') }}</th>
                        <th>{{ __('Date') }}</th>
                        <th>{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($payments as $payment)
                        <tr>
                            <td>
                                <div class="font-semibold text-primary">{{ $payment->user->name }}</div>
                                <div class="text-xs text-muted font-mono">{{ $payment->user->email }}</div>
                            </td>
                            <td>
                                @if($payment->payment_method === 'vodafone_cash')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-red-100 dark:bg-red-900/30 text-red-750 dark:text-red-300">
                                        {{ __('Vodafone Cash') }}
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-yellow-100 dark:bg-yellow-900/30 text-yellow-750 dark:text-yellow-300">
                                        {{ __('Binance Pay') }}
                                    </span>
                                @endif
                            </td>
                            <td>
                                <div class="font-bold text-primary font-mono">${{ number_format($payment->amount_usd, 2) }}</div>
                                @if($payment->payment_method === 'vodafone_cash' && $payment->amount_egp > 0)
                                    <div class="text-xs text-muted font-mono">{{ number_format($payment->amount_egp, 2) }} {{ __('EGP') }}</div>
                                @endif
                            </td>
                            <td class="text-emerald-600 dark:text-emerald-400 font-bold font-mono">
                                +{{ number_format($payment->points) }} {{ __('pts') }}
                            </td>
                            <td>
                                @if($payment->payment_method === 'vodafone_cash')
                                    <div class="text-xs">
                                        <span class="text-muted">{{ __('Phone:') }}</span>
                                        <span class="font-mono text-primary font-semibold">{{ $payment->sender_phone }}</span>
                                    </div>
                                @endif
                                @if($payment->transaction_id)
                                    <div class="text-xs">
                                        <span class="text-muted">{{ __('ID/Order ID:') }}</span>
                                        <span class="font-mono text-primary font-semibold">{{ $payment->transaction_id }}</span>
                                    </div>
                                @endif
                            </td>
                            <td>
                                @if($payment->status === 'success')
                                    <span class="badge-completed">{{ __('Success') }}</span>
                                @elseif($payment->status === 'failed')
                                    <span class="badge-cancelled">{{ __('Failed') }}</span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-blue-100 dark:bg-blue-900/30 text-blue-750 dark:text-blue-300">
                                        {{ __('Pending') }}
                                    </span>
                                @endif
                            </td>
                            <td class="text-muted text-xs font-mono">{{ $payment->created_at->format('M d, Y H:i') }}</td>
                            <td>
                                @if($payment->status === 'pending')
                                    <div class="flex items-center gap-2">
                                        <form method="POST" action="{{ route('admin.payments.approve', $payment) }}" onsubmit="return confirm('{{ __('Approve this transaction and credit points to the user?') }}')">
                                            @csrf
                                            <button type="submit" class="btn-primary btn-sm py-1 px-3 text-xs bg-emerald-600 hover:bg-emerald-700 border-0">{{ __('Approve') }}</button>
                                        </form>
                                        <form method="POST" action="{{ route('admin.payments.reject', $payment) }}" onsubmit="return confirm('{{ __('Reject this transaction?') }}')">
                                            @csrf
                                            <button type="submit" class="btn-danger btn-sm py-1 px-3 text-xs">{{ __('Reject') }}</button>
                                        </form>
                                    </div>
                                @else
                                    <span class="text-muted font-normal text-xs">—</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-10 text-muted">{{ __('No transactions logged yet.') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($payments->hasPages())
            <div class="px-4 py-3" style="border-top: 1px solid var(--border-color);">
                {{ $payments->links() }}
            </div>
        @endif
    </div>
</x-admin-layout>
