<x-admin-layout>
    <x-slot name="title">{{ __('Activity Logs') }}</x-slot>
    <x-slot name="header">{{ __('Activity Logs') }}</x-slot>

    <form method="GET" class="flex flex-wrap gap-2 mb-5">
        <input type="text" name="search" value="{{ request('search') }}"
               placeholder="{{ __('Search descriptions...') }}" class="form-input w-52 py-2 text-sm">
        <select name="action" class="form-input w-44 py-2 text-sm">
            <option value="">{{ __('All Actions') }}</option>
            @foreach($actions as $action)
                <option value="{{ $action }}" {{ request('action') === $action ? 'selected' : '' }}>{{ $action }}</option>
            @endforeach
        </select>
        <input type="date" name="from" value="{{ request('from') }}" class="form-input w-36 py-2 text-sm">
        <input type="date" name="to" value="{{ request('to') }}" class="form-input w-36 py-2 text-sm">
        <button type="submit" class="btn-secondary btn-sm px-4">{{ __('Filter') }}</button>
        @if(request()->hasAny(['search', 'action', 'from', 'to']))
            <a href="{{ route('admin.logs.index') }}" class="btn-secondary btn-sm">{{ __('Clear') }}</a>
        @endif
    </form>

    <div class="card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>{{ __('Action') }}</th>
                        <th>{{ __('Description') }}</th>
                        <th>{{ __('Admin') }}</th>
                        <th>{{ __('IP') }}</th>
                        <th>{{ __('Date') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                        <tr>
                            <td>
                                <span class="badge badge-pending font-mono text-[10px] tracking-wide">{{ $log->action }}</span>
                            </td>
                            <td class="max-w-xs">
                                <span class="text-sm truncate block">{{ $log->description }}</span>
                            </td>
                            <td class="text-sm">{{ $log->admin->name ?? '—' }}</td>
                            <td class="text-xs text-muted font-mono">{{ $log->ip_address ?? '—' }}</td>
                            <td class="text-xs text-muted">{{ $log->created_at->format('M d, Y H:i') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-10 text-muted">{{ __('No logs found') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($logs->hasPages())
            <div class="px-4 py-3" style="border-top: 1px solid var(--border-color);">
                {{ $logs->links() }}
            </div>
        @endif
    </div>
</x-admin-layout>
