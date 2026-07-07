<x-admin-layout>
    <x-slot name="title">{{ __('Admins') }}</x-slot>
    <x-slot name="header">{{ __('Admins Management') }}</x-slot>

    <div class="flex flex-wrap items-center justify-between gap-4 mb-5">
        <form method="GET" class="flex gap-2">
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="{{ __('Search...') }}" class="form-input w-64">
            <button type="submit" class="btn-secondary">{{ __('Search') }}</button>
        </form>
        <a href="{{ route('admin.admins.create') }}" class="btn-primary">+ {{ __('Add Admin') }}</a>
    </div>

    <div class="card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>{{ __('Name') }}</th>
                        <th>{{ __('Email') }}</th>
                        <th>{{ __('Joined') }}</th>
                        <th>{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($admins as $admin)
                        <tr>
                            <td class="font-semibold">{{ $admin->name }}</td>
                            <td>{{ $admin->email }}</td>
                            <td class="text-muted text-sm">{{ $admin->created_at->format('M d, Y') }}</td>
                            <td>
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('admin.admins.edit', $admin) }}" class="btn-secondary btn-sm">{{ __('Edit') }}</a>
                                    
                                    @if($admin->id !== auth()->id())
                                        <form method="POST" action="{{ route('admin.admins.destroy', $admin) }}">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn-danger btn-sm"
                                                    onclick="return confirm('{{ __('Are you sure you want to delete this admin?') }}')">
                                                {{ __('Delete') }}
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center py-10 text-muted">{{ __('No admins found') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($admins->hasPages())
            <div class="px-4 py-3 border-t border-slate-200 dark:border-slate-700">
                {{ $admins->links() }}
            </div>
        @endif
    </div>
</x-admin-layout>
