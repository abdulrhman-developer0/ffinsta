<x-admin-layout>
    <x-slot name="title">{{ __('User Management') }}</x-slot>
    <x-slot name="header">{{ __('Users') }}</x-slot>

    {{-- Filters --}}
    <form method="GET" class="flex flex-wrap gap-2 mb-5">
        <input type="text" name="search" value="{{ request('search') }}"
               placeholder="{{ __('Search name or email...') }}" class="form-input w-56 py-2 text-sm">
        <select name="status" class="form-input w-36 py-2 text-sm">
            <option value="">{{ __('All') }}</option>
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
                        <th>{{ __('User') }}</th>
                        <th>{{ __('Points') }}</th>
                        <th>{{ __('Referrals') }}</th>
                        <th>{{ __('Orders') }}</th>
                        <th>{{ __('Status') }}</th>
                        <th>{{ __('Joined') }}</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                        <tr>
                            <td>
                                <div>
                                    <p class="font-semibold text-primary flex items-center gap-2">
                                        {{ $user->name }}
                                        
                                        @if($user->google_id)
                                            <span title="Google Account" class="text-green-500">
                                                <svg xmlns="http://www.w3.org/2000/svg"
                                                     class="w-4 h-4"
                                                     fill="none"
                                                     viewBox="0 0 24 24"
                                                     stroke="currentColor"
                                                     stroke-width="2">
                                                    <path stroke-linecap="round"
                                                          stroke-linejoin="round"
                                                          d="M5 13l4 4L19 7" />
                                                </svg>
                                            </span>
                                        @endif
                                        
                                    </p>
                                    <p class="text-xs text-muted">{{ $user->email }}</p>
                                </div>
                            </td>
                            <td class="font-semibold text-primary">{{ number_format($user->points) }}</td>
                            <td>{{ $user->referrals_count }}</td>
                            <td>{{ $user->orders_count ?? 0 }}</td>
                            <td>
                                @if($user->is_suspended)
                                    <span class="badge badge-cancelled">{{ __('Suspended') }}</span>
                                @else
                                    <span class="badge badge-completed">{{ __('Active') }}</span>
                                @endif
                            </td>
                            <td class="text-muted text-xs">{{ $user->created_at->format('M d, Y') }}</td>
                            <td>
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('admin.users.show', $user) }}"
                                       class="btn-secondary btn-sm">{{ __('View') }}</a>
                                    <form method="POST" action="{{ route('admin.users.suspend', $user) }}">
                                        @csrf @method('PATCH')
                                        <button type="submit"
                                                class="{{ $user->is_suspended ? 'btn-success' : 'btn-danger' }} btn-sm">
                                            {{ $user->is_suspended ? __('Unsuspend') : __('Suspend') }}
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-10 text-muted">{{ __('No users found') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($users->hasPages())
            <div class="px-4 py-3" style="border-top: 1px solid var(--border-color);">
                {{ $users->links() }}
            </div>
        @endif
    </div>
</x-admin-layout>
