<x-admin-layout>
    <x-slot name="title">{{ __('Referral Management') }}</x-slot>
    <x-slot name="header">{{ __('Referral') }}</x-slot>
    
    {{-- Filters --}}
    <form method="GET" class="flex flex-wrap gap-2 mb-5">
        <input type="text" name="search" value="{{ request('search') }}"
               placeholder="{{ __('Search name or email...') }}" class="form-input w-56 py-2 text-sm">
        <button type="submit" class="btn-secondary btn-sm px-4">{{ __('Filter') }}</button>
    </form>


<div class="card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>{{ __('User') }}</th>
                        <th>{{ __('Referral Code') }}</th>
                        <th>{{ __('Total Referrals') }}</th>
                        <th>{{ __('Total Bonus') }}</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                        @php
                            $totalBonus = $user->referrals->sum('points_awarded');
                        @endphp
                        
                        <tr>
                            <td>
                                <div>
                                    <div class="font-semibold">
                                        {{ $user->name }}
                                    </div>
                                    <div class="text-xs text-muted">
                                        {{ $user->email }}
                                    </div>
                                </div>
                            </td>

                            <td>
                                {{ $user->referral_code }}
                            </td>

                            <td>
                                {{ $user->referrals_count }}
                            </td>

                            <td>
                                {{ number_format($totalBonus) }}
                            </td>

                            <td>
                                <a
                                    href="{{ route('admin.referrals.show',$user) }}"
                                    class="btn-secondary btn-sm">
                                    View
                                </a>
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