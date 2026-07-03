<x-app-layout>
    <x-slot name="title">{{ __('Task History') }}</x-slot>
    <x-slot name="header">{{ __('My Task History') }}</x-slot>

    <div class="card overflow-hidden">
        <div class="px-5 py-4 flex items-center justify-between" style="border-bottom: 1px solid var(--border-color);">
            <h2 class="section-title">{{ __('Recent Tasks') }}</h2>
        </div>

        @if($completions->isEmpty())
            <div class="p-8 text-center text-muted">
                <p>{{ __('You have not attempted any tasks yet.') }}</p>
                <a href="{{ route('user.earn.index') }}" class="btn-primary btn-sm mt-3 inline-flex">{{ __('Earn Points') }}</a>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>{{ __('Task') }}</th>
                            <th>{{ __('Status') }}</th>
                            <th>{{ __('Points') }}</th>
                            <th>{{ __('Date') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($completions as $completion)
                            <tr>
                                <td>
                                    <div class="flex items-center gap-2">
                                        <div class="w-8 h-8 rounded-full bg-gradient-to-br from-purple-500 to-pink-500 flex items-center justify-center text-white text-xs font-bold">
                                            {{ strtoupper(substr($completion->task->requester_instagram_username, 0, 1)) }}
                                        </div>
                                        <div>
                                            <p class="font-medium text-primary">{{ '@' . $completion->task->requester_instagram_username }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    @if($completion->status === 'verified')
                                        <span class="badge badge-success">{{ __('Verified') }}</span>
                                    @elseif($completion->status === 'pending')
                                        <span class="badge badge-warning mb-2 block">{{ $completion->verification_stage ?? __('Pending Verification') }}</span>
                                        @if($completion->updated_at->diffInMinutes(now()) >= 2)
                                            <form action="{{ route('user.earn.check_status', $completion->id) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-primary py-1 px-2 text-xs">
                                                    {{ __('Check Status') }}
                                                </button>
                                            </form>
                                        @endif
                                    @elseif($completion->status === 'failed')
                                        <span class="badge badge-danger">{{ __('Failed') }}</span>
                                        @if($completion->reason)
                                            <p class="text-xs text-red-500 mt-1 max-w-[200px]">{{ $completion->reason }}</p>
                                        @endif
                                    @else
                                        <span class="badge badge-secondary">{{ ucfirst($completion->status) }}</span>
                                    @endif
                                </td>
                                <td>
                                    @if($completion->status === 'verified')
                                        <span class="text-emerald-600 dark:text-emerald-400 font-bold">+{{ $completion->task->reward_points }} pts</span>
                                    @else
                                        <span class="text-muted">0 pts</span>
                                    @endif
                                </td>
                                <td class="text-xs text-muted whitespace-nowrap">
                                    {{ $completion->created_at->format('M d, Y H:i') }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if($completions->hasPages())
                <div class="px-4 py-3" style="border-top: 1px solid var(--border-color);">
                    {{ $completions->links() }}
                </div>
            @endif
        @endif
    </div>
</x-app-layout>
