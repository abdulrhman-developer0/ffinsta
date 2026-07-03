<x-admin-layout>
    <x-slot name="title">{{ $order->order_number }}</x-slot>
    <x-slot name="header">{{ __('Order Details') }}</x-slot>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Order Summary --}}
        <div class="space-y-5">
            <div class="card p-5">
                <div class="flex items-start justify-between mb-4">
                    <div>
                        <p class="font-mono font-bold text-primary text-lg">{{ $order->order_number }}</p>
                        <p class="text-sm text-muted">{{ '@' . $order->instagram_username }}</p>
                    </div>
                    <span class="badge-{{ $order->status }}">{{ ucfirst($order->status) }}</span>
                </div>

                {{-- Progress --}}
                <div class="mb-4">
                    <div class="flex justify-between text-sm mb-1">
                        <span class="text-muted">{{ __('Progress') }}</span>
                        <span class="font-semibold text-primary">{{ $order->delivered_qty }}/{{ $order->requested_qty }}</span>
                    </div>
                    <div class="progress-bar h-3">
                        <div class="progress-fill" style="width: {{ $order->progressPercent() }}%"></div>
                    </div>
                </div>

                <div class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="text-muted">{{ __('User') }}</span>
                        <a href="{{ route('admin.users.show', $order->user) }}" class="text-brand-500 hover:underline">{{ $order->user->name }}</a>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-muted">{{ __('Priority') }}</span>
                        <span class="badge-{{ $order->priority }}">{{ ucfirst($order->priority) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-muted">{{ __('Points Cost') }}</span>
                        <span class="font-semibold text-primary">{{ number_format($order->points_cost) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-muted">{{ __('Created') }}</span>
                        <span class="text-primary">{{ $order->created_at->format('M d, Y H:i') }}</span>
                    </div>
                </div>

                @if($order->admin_notes)
                    <div class="mt-4 p-3 rounded-xl bg-surface-3 text-sm">
                        <p class="text-xs text-muted mb-1">{{ __('Admin Notes') }}</p>
                        {{ $order->admin_notes }}
                    </div>
                @endif

                {{-- Actions --}}
                <div class="divider my-4"></div>
                <div class="flex flex-col gap-2">
                    @if($order->status === 'pending')
                        <form method="POST" action="{{ route('admin.orders.activate', $order) }}">
                            @csrf
                            <button type="submit" class="btn-success w-full">{{ __('Activate Order') }}</button>
                        </form>
                    @endif
                    @if(!in_array($order->status, ['completed', 'cancelled']))
                        <form method="POST" action="{{ route('admin.orders.update', $order) }}"
                              onsubmit="return confirm('{{ __('Mark this order as completed?') }}')">
                            @csrf @method('PATCH')
                            <input type="hidden" name="status" value="completed">
                            <button type="submit" class="btn-success w-full">{{ __('Mark as Completed') }}</button>
                        </form>
                        <form method="POST" action="{{ route('admin.orders.cancel', $order) }}"
                              onsubmit="return confirm('{{ __('Cancel this order and refund points?') }}')">
                            @csrf
                            <input type="hidden" name="refund" value="1">
                            <button type="submit" class="btn-danger w-full">{{ __('Cancel & Refund') }}</button>
                        </form>
                    @endif
                    <form method="POST" action="{{ route('admin.orders.destroy', $order) }}"
                          onsubmit="return confirm('{{ __('Permanently delete this order?') }}')">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn-danger btn-sm w-full opacity-70 hover:opacity-100">{{ __('Delete') }}</button>
                    </form>
                </div>
            </div>
        </div>

        {{-- Follow Tasks --}}
        <div class="lg:col-span-2">
            <div class="card overflow-hidden">
                <div class="px-5 py-3" style="border-bottom: 1px solid var(--border-color);">
                    <h3 class="section-title">{{ __('Follow Tasks') }} ({{ $order->followTasks->count() }})</h3>
                </div>
                <div class="overflow-x-auto max-h-96">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>{{ __('Assigned To') }}</th>
                                <th>{{ __('Status') }}</th>
                                <th>{{ __('Updated') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($order->followTasks as $task)
                                <tr>
                                    <td class="text-muted text-xs">{{ $task->id }}</td>
                                    <td>
                                        @if($task->assignedUser)
                                            <a href="{{ route('admin.users.show', $task->assignedUser) }}" class="text-brand-500 hover:underline text-sm">
                                                {{ $task->assignedUser->name }}
                                            </a>
                                        @else
                                            <span class="text-muted text-sm">—</span>
                                        @endif
                                    </td>
                                    <td><span class="badge-{{ $task->status }}">{{ ucfirst($task->status) }}</span></td>
                                    <td class="text-muted text-xs">{{ $task->updated_at->diffForHumans() }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-6 text-muted">{{ __('No tasks yet') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>
