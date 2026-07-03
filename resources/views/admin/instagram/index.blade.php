<x-admin-layout>
    <x-slot name="title">{{ __('Instagram Accounts') }}</x-slot>
    <x-slot name="header">{{ __('Instagram Accounts') }}</x-slot>

    <form method="GET" class="flex flex-wrap gap-2 mb-5">
        <input type="text" name="search" value="{{ request('search') }}"
               placeholder="{{ __('Search username or user…') }}" class="form-input w-52 py-2 text-sm">
        <select name="status" class="form-input w-36 py-2 text-sm">
            <option value="">{{ __('All Statuses') }}</option>
            <option value="active"    {{ request('status') === 'active' ? 'selected' : '' }}>{{ __('Active') }}</option>
            <option value="suspended" {{ request('status') === 'suspended' ? 'selected' : '' }}>{{ __('Suspended') }}</option>
        </select>
        <button type="submit" class="btn-secondary btn-sm px-4">{{ __('Filter') }}</button>
    </form>

    <div class="card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>{{ __('Username') }}</th>
                        <th>{{ __('Owner') }}</th>
                        <th>{{ __('Default') }}</th>
                        <th>{{ __('Status') }}</th>
                        <th>{{ __('Added') }}</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($accounts as $account)
                        <tr>
                            <td class="font-medium text-primary">{{ '@' . $account->username }}</td>
                            <td>
                                <a href="{{ route('admin.users.show', $account->user) }}" class="text-brand-500 hover:underline text-sm">
                                    {{ $account->user->name }}
                                </a>
                            </td>
                            <td>
                                @if($account->is_default)
                                    <span class="badge badge-completed">{{ __('Default') }}</span>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td><span class="badge-{{ $account->status }}">{{ ucfirst($account->status) }}</span></td>
                            <td class="text-muted text-xs">{{ $account->created_at->format('M d, Y') }}</td>
                            <td>
                                <form method="POST" action="{{ route('admin.instagram.status', $account) }}">
                                    @csrf @method('PATCH')
                                    <select name="status" onchange="this.form.submit()" class="form-input text-xs py-1.5 w-28">
                                        <option value="active"    {{ $account->status === 'active' ? 'selected' : '' }}>{{ __('Active') }}</option>
                                        <option value="suspended" {{ $account->status === 'suspended' ? 'selected' : '' }}>{{ __('Suspend') }}</option>
                                    </select>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-10 text-muted">{{ __('No accounts found') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($accounts->hasPages())
            <div class="px-4 py-3" style="border-top: 1px solid var(--border-color);">
                {{ $accounts->links() }}
            </div>
        @endif
    </div>
</x-admin-layout>
