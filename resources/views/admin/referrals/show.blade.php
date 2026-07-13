<x-admin-layout>
    <x-slot name="title">
        {{ $user->name }} Referrals
    </x-slot>

    <x-slot name="header">
        {{ $user->name }} Referrals
    </x-slot>
    <div class="card overflow-hiddenb mb-6 p-4">
        <h3 class="font-bold text-lg">
            {{ $user->name }}
        </h3>

        <p class="text-muted">
            {{ $user->email }}
        </p>

        <p class="mt-2">
            Referral Code:
            <strong>{{ $user->referral_code }}</strong>
        </p>
    </div>
    
     <div class="card overflow-hidden">
         
        <div class="overflow-x-auto">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>{{ __('Referred User') }}</th>
                        <th>{{ __('Email') }}</th>
                        <th>{{ __('Bonus') }}</th>
                        <th>{{ __('Joined') }}</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($referrals as $referral)
                        <tr>
                            <td class="font-semibold text-primary">{{ $referral->referee->name }}</td>
                            <td class="font-semibold text-primary">{{ $referral->referee->email }}</td>
                            <td class="font-semibold text-primary">{{ $referral->points_awarded }}</td>
                            <td class="font-semibold text-primary">{{ $referral->created_at->diffForHumans() }}</td>
                            
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-10 text-muted">{{ __('No users found') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($referrals->hasPages())
            <div class="px-4 py-3" style="border-top: 1px solid var(--border-color);">
                {{ $referrals->links() }}
            </div>
        @endif
    </div>
</x-admin-layout>