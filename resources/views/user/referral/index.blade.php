<x-app-layout>
    <x-slot name="title">{{ __('Referral Program') }}</x-slot>
    <x-slot name="header">{{ __('Referral Program') }}</x-slot>

    <div class="max-w-2xl mx-auto space-y-6">
        {{-- Referral Link Card --}}
        <div class="card p-6 bg-gradient-to-br from-brand-500 to-brand-700 text-white border-0">
            <h2 class="text-lg font-bold mb-1">{{ __('Share & Earn Points') }}</h2>
            <p class="text-sm opacity-80 mb-4">{{ __('Earn bonus points for every friend who joins using your link.') }}</p>

            <div class="bg-white/20 rounded-xl p-3 flex items-center gap-2">
                <input type="text" id="referral-link" value="{{ $referralLink }}" readonly
                       class="bg-transparent text-white text-sm flex-1 focus:outline-none truncate font-mono">
                <button onclick="navigator.clipboard.writeText('{{ $referralLink }}').then(() => alert('{{ __('Link copied!') }}'))"
                        class="btn bg-white/30 hover:bg-white/40 text-white btn-sm flex-shrink-0">
                    {{ __('Copy') }}
                </button>
            </div>

            <div class="mt-3 flex items-center gap-1.5">
                <span class="text-xs opacity-70">{{ __('Your code') }}:</span>
                <span class="font-mono font-bold text-sm bg-white/20 px-2.5 py-0.5 rounded-lg">{{ auth()->user()->referral_code }}</span>
            </div>
        </div>

        {{-- Stats --}}
        <div class="grid grid-cols-2 gap-4">
            <div class="stat-card">
                <div class="stat-icon bg-purple-100 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-xs text-muted">{{ __('Total Referrals') }}</p>
                    <p class="text-2xl font-bold text-primary">{{ $referrals->count() }}</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-xs text-muted">{{ __('Points Earned') }}</p>
                    <p class="text-2xl font-bold text-primary">{{ number_format($totalPointsEarned) }}</p>
                </div>
            </div>
        </div>

        {{-- Referrals Table --}}
        @if($referrals->isNotEmpty())
            <div class="card overflow-hidden">
                <div class="px-5 py-3" style="border-bottom: 1px solid var(--border-color);">
                    <h3 class="section-title">{{ __('Referred Users') }}</h3>
                </div>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>{{ __('User') }}</th>
                            <th>{{ __('Points Awarded') }}</th>
                            <th>{{ __('Joined') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($referrals as $referral)
                            <tr>
                                <td>{{ $referral->referee->name }}</td>
                                <td class="text-emerald-600 dark:text-emerald-400 font-semibold">+{{ $referral->points_awarded }}</td>
                                <td class="text-muted text-xs">{{ $referral->created_at->format('M d, Y') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</x-app-layout>
