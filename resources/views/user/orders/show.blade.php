<x-app-layout>
    <x-slot name="title">{{ __('Order') }} {{ $order->order_number }}</x-slot>
    <x-slot name="header">{{ __('Order Details') }}</x-slot>

    <div class="max-w-2xl mx-auto space-y-6">
        {{-- Order Info Card --}}
        <div class="card p-6">
            <div class="flex items-start justify-between mb-4">
                <div>
                    <p class="font-mono text-lg font-bold text-primary">{{ $order->order_number }}</p>
                    <p class="text-sm text-muted mt-0.5">{{ '@' . $order->instagram_username }}</p>
                </div>
                <span class="badge-{{ $order->status }} text-sm">{{ ucfirst($order->status) }}</span>
            </div>

            {{-- Progress --}}
            <div class="mb-5">
                <div class="flex justify-between text-sm mb-1.5">
                    <span class="text-muted">{{ __('Progress') }}</span>
                    <span class="font-semibold text-primary">{{ $order->delivered_qty }} / {{ $order->requested_qty }}</span>
                </div>
                <div class="progress-bar h-3">
                    <div class="progress-fill" style="width: {{ $order->progressPercent() }}%"></div>
                </div>
                <p class="text-xs text-muted mt-1">{{ $order->progressPercent() }}% {{ __('complete') }}</p>
            </div>

            <div class="grid grid-cols-2 gap-4 text-sm">
                <div>
                    <p class="text-muted">{{ __('Points Spent') }}</p>
                    <p class="font-semibold text-primary">{{ number_format($order->points_cost) }} pts</p>
                </div>
                <div>
                    <p class="text-muted">{{ __('Priority') }}</p>
                    <span class="badge-{{ $order->priority }}">{{ ucfirst($order->priority) }}</span>
                </div>
                <div>
                    <p class="text-muted">{{ __('Created') }}</p>
                    <p class="font-medium text-primary">{{ $order->created_at->format('M d, Y H:i') }}</p>
                </div>
                @if($order->admin_notes)
                    <div class="col-span-2">
                        <p class="text-muted mb-1">{{ __('Admin Notes') }}</p>
                        <p class="text-sm bg-surface-3 rounded-lg px-3 py-2">{{ $order->admin_notes }}</p>
                    </div>
                @endif
            </div>
        </div>

        <div class="text-center">
            <a href="{{ route('user.orders.index') }}" class="btn-secondary btn-sm">
                ← {{ __('Back to Orders') }}
            </a>
        </div>
    </div>
</x-app-layout>
