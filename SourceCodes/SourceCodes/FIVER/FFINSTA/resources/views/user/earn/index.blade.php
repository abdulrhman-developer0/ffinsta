<x-app-layout>
    <x-slot name="title">{{ __('Earn Points') }}</x-slot>

    {{-- Status alerts --}}
    @if(session('success'))
        <div class="alert-success mb-4">{{ session('success') }}</div>
    @endif
    @error('task') <div class="alert-error mb-4">{{ $message }}</div> @enderror

    {{-- Stats Row --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        {{-- My Points --}}
        <div class="card p-4 flex flex-col justify-center gap-2 border-l-4 border-l-emerald-500">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-emerald-500/10 flex items-center justify-center text-emerald-500">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" /></svg>
                </div>
                <div>
                    <p class="text-xs text-muted mb-0.5">{{ __('My Points') }}</p>
                    <p class="font-bold text-emerald-500 text-sm">{{ auth()->user()->points }} {{ __('pts') }}</p>
                </div>
            </div>
        </div>

        {{-- Tasks Completed Today --}}
        <div class="card p-4 flex flex-col justify-center gap-2 border-l-4 border-l-blue-500">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-blue-500/10 flex items-center justify-center text-blue-500">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                </div>
                <div>
                    <p class="text-xs text-muted mb-0.5">{{ __('Tasks Completed Today') }}</p>
                    <p class="font-bold text-blue-500 text-sm">{{ $completedTasksToday }} {{ __('tasks') }}</p>
                </div>
            </div>
        </div>

        {{-- Daily Progress --}}
        <div class="card p-4 flex flex-col justify-center gap-2 border-l-4 border-l-slate-400 dark:border-l-slate-600">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-slate-500/10 flex items-center justify-center text-slate-500 dark:text-slate-400">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" /></svg>
                </div>
                <div>
                    <p class="text-xs text-muted mb-0.5">{{ __('Daily Progress') }}</p>
                    <p class="font-bold text-slate-600 dark:text-slate-300 text-sm">{{ $completedTasksToday }} / {{ $dailyLimit }}</p>
                </div>
            </div>
        </div>

        {{-- Hourly Progress --}}
        <div class="card p-4 flex flex-col justify-center gap-2 border-l-4 border-l-amber-500 dark:border-l-amber-600">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-amber-500/10 flex items-center justify-center text-amber-500 dark:text-amber-400">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                </div>
                <div>
                    <p class="text-xs text-muted mb-0.5">{{ __('Hourly Progress') }}</p>
                    <p class="font-bold text-amber-600 dark:text-amber-400 text-sm">{{ $completedTasksThisHour }} / {{ $maxTasksPerHour }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Account Selection --}}
    <div class="mb-8">
        <div class="flex items-center justify-center gap-4 mb-4">
            <hr class="w-12 border-[var(--border-color)]">
            <h3 class="text-sm font-medium text-muted flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                {{ __('Choose Account to Follow With') }}
            </h3>
            <hr class="w-12 border-[var(--border-color)]">
        </div>
        
        <div class="flex overflow-x-auto gap-4 pb-2 hide-scrollbar">
            @forelse($userAccounts as $index => $acc)
                <label class="cursor-pointer flex-shrink-0">
                    <input type="radio" name="selected_account_radio" value="{{ $acc->id }}" class="peer sr-only" {{ $index === 0 ? 'checked' : '' }} onchange="document.getElementById('selected_account_id') ? document.getElementById('selected_account_id').value = this.value : null;">
                    <div class="card px-4 py-3 flex items-center gap-3 border-2 border-transparent peer-checked:border-brand-500 peer-checked:bg-brand-500/5 transition-all">
                        <div class="w-10 h-10 rounded-full bg-gradient-to-br from-purple-500 to-pink-500 flex items-center justify-center text-white font-black shadow-sm">
                            {{ strtoupper(substr($acc->username, 0, 1)) }}
                        </div>
                        <div class="flex flex-col pr-2">
                            <span class="text-sm font-bold text-primary">{{ $acc->username }}</span>
                            <span class="text-xs text-muted">{{ '@' . $acc->username }}</span>
                        </div>
                        <div class="ml-2 w-5 h-5 rounded-full bg-brand-500 text-white flex items-center justify-center opacity-0 peer-checked:opacity-100 transition-opacity">
                            <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" /></svg>
                        </div>
                    </div>
                </label>
            @empty
                <div class="card px-6 py-4 flex-1 text-center border border-dashed border-slate-300 dark:border-slate-700">
                    <p class="text-sm text-muted mb-2">{{ __('Add an Instagram account to start earning.') }}</p>
                </div>
            @endforelse
            
            <a href="{{ route('user.instagram.create') }}" class="card px-6 py-3 flex-shrink-0 flex items-center justify-center gap-2 border border-dashed border-slate-300 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-muted" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                <span class="text-sm font-medium text-muted">{{ __('Add New Account') }}</span>
            </a>
        </div>
        <p class="text-center text-xs text-muted mt-3 flex items-center justify-center gap-1">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            {{ __('The selected Instagram account will open when clicking follow') }}
        </p>
    </div>

    {{-- Current Task --}}
    <div class="flex items-center justify-center gap-4 mb-4">
        <hr class="w-12 border-[var(--border-color)]">
        <h3 class="text-sm font-medium text-muted">{{ __('Current Task') }}</h3>
        <hr class="w-12 border-[var(--border-color)]">
    </div>

    @php
        $hasTasks = $myAssignedTasks->isNotEmpty() || $availableOrders->isNotEmpty();
    @endphp

    @if($hasTasks)
        @if(!empty($activeTask))
            @php
                $task = $activeTask;
                $order = null;
                $displayUsername = (string) $task->requester_instagram_username;
                $displayAvatar = $task->order->profile_picture_url ?? $task->order->instagramAccount?->profile_picture_url ?? \App\Models\InstagramProfileCache::where('username', $displayUsername)->value('profile_picture_url');
            @endphp
            @include('user.earn.partials.task_card')
        @endif

        @foreach($myAssignedTasks as $task)
            @php
                $order = null;
                $displayUsername = (string) $task->requester_instagram_username;
                $displayAvatar = $task->order->profile_picture_url ?? $task->order->instagramAccount?->profile_picture_url ?? \App\Models\InstagramProfileCache::where('username', $displayUsername)->value('profile_picture_url');
            @endphp
            @include('user.earn.partials.task_card')
        @endforeach

        @foreach($availableOrders as $order)
            @php
                $task = null;
                $displayUsername = (string) $order->instagram_username;
                $displayAvatar = $order->profile_picture_url ?? $order->instagramAccount?->profile_picture_url ?? \App\Models\InstagramProfileCache::where('username', $displayUsername)->value('profile_picture_url');
            @endphp
            @include('user.earn.partials.task_card')
        @endforeach

        <div class="flex justify-center mb-8">
            @if(isset($refreshWaitRemaining) && $refreshWaitRemaining > 0)
                <button type="button" disabled class="py-2.5 px-6 rounded-xl border border-[var(--border-color)] bg-[var(--bg-secondary)] text-muted font-bold flex items-center gap-2 cursor-not-allowed shadow-sm">
                    <svg class="animate-spin h-5 w-5 mr-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                    {{ __('Wait') }} <span id="refresh-timer" class="ml-1">{{ $refreshWaitRemaining }}</span>s
                </button>
                <script>
                    (function() {
                        let sec = parseInt('{{ $refreshWaitRemaining }}') || 0;
                        const span = document.getElementById('refresh-timer');
                        const iv = setInterval(() => {
                            sec--;
                            if(sec <= 0) {
                                clearInterval(iv);
                                window.location.reload();
                            } else {
                                if(span) span.textContent = sec;
                            }
                        }, 1000);
                    })();
                </script>
            @else
                <a href="{{ route('user.earn.index', ['action' => 'refresh']) }}" class="py-2.5 px-6 rounded-xl border border-[var(--border-color)] bg-[var(--bg-secondary)] hover:bg-[var(--bg-tertiary)] text-primary font-bold flex items-center gap-2 transition-colors shadow-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg>
                    {{ __('Refresh Task') }}
                </a>
            @endif
        </div>
    @else
        <div class="card p-12 text-center text-muted mb-8">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-16 h-16 mx-auto mb-4 text-slate-300 dark:text-slate-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <p class="text-lg font-bold text-primary mb-2">{{ __('No tasks available') }}</p>
            <p class="text-sm">{{ __('No tasks available right now. Check back soon!') }}</p>
        </div>
    @endif
</x-app-layout>
