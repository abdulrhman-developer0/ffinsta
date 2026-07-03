@php
    $cardId = $task ? 'task-'.$task->id : 'order-'.$order->id;
@endphp
            <div class="card p-4 sm:p-6 mb-6 bg-[var(--bg-card)] border border-[var(--border-color)] shadow-md rounded-2xl">
            <div class="flex flex-col gap-6">
                
                {{-- Top Section: Profile and Steps --}}
                <div class="flex flex-col lg:flex-row gap-6">
                    
                    {{-- Left: Profile Info & Actions --}}
                    <div class="flex-1 flex flex-col sm:flex-row sm:items-center justify-between gap-6 overflow-hidden min-w-0">
                        <div class="flex items-center gap-4 sm:gap-5 text-left select-none overflow-hidden min-w-0">
                        <div class="relative flex-shrink-0">
                            @if($displayAvatar)
                                <img src="{{ route('proxy.image', ['url' => $displayAvatar]) }}" alt="{{ $displayUsername }}" class="w-20 h-20 sm:w-28 sm:h-28 rounded-full border-4 border-slate-200 dark:border-slate-800 object-cover shadow-md">
                            @else
                                <div class="w-20 h-20 sm:w-28 sm:h-28 rounded-full bg-gradient-to-br from-slate-200 to-slate-300 dark:from-slate-700 dark:to-slate-800 flex items-center justify-center text-primary text-3xl sm:text-4xl font-black shadow-md border-4 border-slate-200 dark:border-slate-800">
                                    {{ strtoupper(substr($displayUsername, 0, 1)) }}
                                </div>
                            @endif
                            <div class="absolute bottom-0 right-0 w-7 h-7 sm:w-8 sm:h-8 rounded-full bg-gradient-to-tr from-orange-500 via-pink-500 to-purple-500 flex items-center justify-center text-white shadow border-2 border-[var(--bg-card)]">
                                <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/></svg>
                            </div>
                        </div>
                        <div class="flex flex-col items-start text-left overflow-hidden min-w-0 w-full">
                            <p class="font-bold text-xl sm:text-2xl text-primary mb-1 truncate w-full" title="{{ $displayUsername }}">{{ $displayUsername }}</p>
                            <p class="text-sm text-muted mb-3 truncate w-full" title="{{ '@' . $displayUsername }}">{{ '@' . $displayUsername }}</p>
                            <div class="inline-flex items-center justify-center w-fit px-4 py-1.5 rounded-full bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 font-bold text-sm">
                                +{{ $task ? $task->reward_points : $rewardPoints }} {{ __('pts') }}
                            </div>
                            @if($task)
                                @php
                                    $lockMinutes = (int) app(\App\Services\SettingService::class)->get('task_lock_minutes', 10);
                                    $expiresAt = $task->updated_at->addMinutes($lockMinutes);
                                    $remainingTaskSeconds = (int) max(0, now()->diffInSeconds($expiresAt, false));
                                @endphp
                                <p class="text-xs text-amber-600 dark:text-amber-400 mt-3 font-medium flex items-center gap-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                    {{ __('Expires in') }}: <span id="task-expiry-{{ $cardId }}">{{ gmdate("i:s", $remainingTaskSeconds) }}</span>
                                </p>
                                <script>
                                    (function() {
                                        let seconds = parseInt('{{ $remainingTaskSeconds }}') || 0;
                                        const span = document.getElementById('task-expiry-{{ $cardId }}');
                                        const interval = setInterval(() => {
                                            seconds--;
                                            if (seconds <= 0) {
                                                clearInterval(interval);
                                                window.location.reload();
                                            } else {
                                                const m = Math.floor(seconds / 60).toString().padStart(2, '0');
                                                const s = (seconds % 60).toString().padStart(2, '0');
                                                if(span) span.textContent = m + ":" + s;
                                            }
                                        }, 1000);
                                    })();
                                </script>
                            @endif
                        </div>
                    </div>

                        {{-- Actions Stacked --}}
                        <div class="flex flex-col gap-3 w-full sm:w-auto sm:min-w-[220px]">
                            @if($order)
                                {{-- Not claimed yet --}}
                                <form method="POST" action="{{ route('user.earn.claim', $order) }}" class="w-full" onsubmit="window.open('https://instagram.com/{{ urlencode($order->instagram_username) }}', '_blank');">
                                    @csrf
                                    <input type="hidden" name="instagram_account_id" id="selected_account_id_{{ $cardId }}" value="{{ $userAccounts->first()?->id ?? '' }}">
                                    <button type="submit" class="w-full py-3 px-4 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl flex items-center justify-center gap-2 transition-all shadow-sm text-[15px]" onclick="if(!document.getElementById('selected_account_id_{{ $cardId }}').value){alert('{{ __('Please add an account first') }}');return false;}">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/></svg>
                                        {{ __('Follow Account') }}
                                    </button>
                                </form>
                                <button type="button" disabled class="w-full py-3 px-4 bg-transparent border border-[var(--border-color)] text-slate-400 dark:text-slate-600 font-bold rounded-xl flex items-center justify-center gap-2 cursor-not-allowed text-[15px] rtl:flex-row-reverse ltr:flex-row">
                                    <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                    <span>{{ __('Verify & Receive Points') }}</span>
                                </button>
                            @else
                                {{-- Already claimed --}}
                                @php
                                    $pendingCompletion = $task->completions()->where('status', 'pending')->latest()->first();
                                    $attempts = $pendingCompletion ? $pendingCompletion->verification_attempts : 0;
                                    $waitTime = $attempts == 0 ? 15 : 30;
                                    $elapsedSeconds = $task->complete_clicked_at ? abs(now()->diffInSeconds($task->complete_clicked_at, false)) : 0;
                                    $showButton = $task->complete_clicked_at ? ($elapsedSeconds >= $waitTime) : true;
                                    $remainingSeconds = (int) max(0, $waitTime - $elapsedSeconds);
                                @endphp

                                <a href="https://instagram.com/{{ urlencode($task->requester_instagram_username) }}" target="_blank" class="w-full py-3 px-4 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl flex items-center justify-center gap-2 transition-all shadow-sm text-[15px]">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/></svg>
                                    {{ __('Follow Account') }}
                                </a>
                                
                                @if(is_null($task->complete_clicked_at))
                                    <form method="POST" action="{{ route('user.earn.complete', $task) }}" class="w-full">
                                        @csrf
                                        <button type="submit" class="w-full py-3 px-4 bg-transparent border border-blue-600 hover:bg-blue-600/10 text-blue-600 dark:text-blue-400 font-bold rounded-xl flex items-center justify-center gap-2 transition-all shadow-sm text-[15px] rtl:flex-row-reverse ltr:flex-row">
                                            <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                            <span>{{ __('Verify & Receive Points') }}</span>
                                        </button>
                                    </form>
                                @else
                                    @if($pendingCompletion)
                                        <div class="w-full" id="check-container-{{ $cardId }}">
                                            <form method="POST" action="{{ route('user.earn.check_status', $pendingCompletion->id) }}" class="w-full h-full">
                                                @csrf
                                                <button type="submit" class="w-full h-full py-3 px-4 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl flex items-center justify-center gap-2 transition-all shadow-md text-[15px] rtl:flex-row-reverse ltr:flex-row">
                                                    <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg>
                                                    <span>{{ __('Check Status') }}</span>
                                                </button>
                                            </form>
                                        </div>
                                        @if(!$showButton)
                                            <div class="w-full flex items-center justify-center py-3 px-4 bg-[var(--bg-tertiary)] border-2 border-[var(--border-color)] text-muted font-bold rounded-xl text-[15px]" id="wait-container-{{ $cardId }}">
                                                <svg class="animate-spin h-5 w-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                                {{ __('Wait') }} <span id="v-timer-{{ $cardId }}" class="ml-1">{{ $remainingSeconds }}</span>s
                                            </div>
                                            <script>
                                                document.getElementById('check-container-{{ $cardId }}').style.display = 'none';
                                                (function() {
                                                    let sec = parseInt('{{ $remainingSeconds }}') || 0;
                                                    const iv = setInterval(() => {
                                                        sec--;
                                                        if(sec <= 0) {
                                                            clearInterval(iv);
                                                            if(document.getElementById('wait-container-{{ $cardId }}')) document.getElementById('wait-container-{{ $cardId }}').style.display = 'none';
                                                            if(document.getElementById('check-container-{{ $cardId }}')) document.getElementById('check-container-{{ $cardId }}').style.display = 'block';
                                                        } else {
                                                            if(document.getElementById('v-timer-{{ $cardId }}')) document.getElementById('v-timer-{{ $cardId }}').textContent = sec;
                                                        }
                                                    }, 1000);
                                                })();
                                            </script>
                                        @endif
                                    @endif
                                @endif
                            @endif
                        </div>
                    </div>

                    {{-- Right: Steps --}}
                    <div class="hidden md:block flex-1 bg-slate-950/10 dark:bg-slate-950/20 rounded-2xl p-6 border border-[var(--border-color)]">
                        <h4 class="font-bold text-md mb-5 text-primary rtl:text-right ltr:text-left">{{ __('Task Steps') }}</h4>
                        <div class="space-y-4">
                            <div class="flex items-center gap-3">
                                <div class="w-6 h-6 rounded-full bg-blue-600 text-white flex items-center justify-center font-bold text-xs shadow flex-shrink-0">1</div>
                                <p class="text-sm font-medium text-muted flex-1 rtl:text-right ltr:text-left">{{ __('Click on Follow Account button') }}</p>
                            </div>
                            <div class="flex items-center gap-3">
                                <div class="w-6 h-6 rounded-full bg-blue-600 text-white flex items-center justify-center font-bold text-xs shadow flex-shrink-0">2</div>
                                <p class="text-sm font-medium text-muted flex-1 rtl:text-right ltr:text-left">{{ __('Instagram will open') }}</p>
                            </div>
                            <div class="flex items-center gap-3">
                                <div class="w-6 h-6 rounded-full bg-blue-600 text-white flex items-center justify-center font-bold text-xs shadow flex-shrink-0">3</div>
                                <p class="text-sm font-medium text-muted flex-1 rtl:text-right ltr:text-left">{{ __('Follow the account') }}</p>
                            </div>
                            <div class="flex items-center gap-3">
                                <div class="w-6 h-6 rounded-full bg-blue-600 text-white flex items-center justify-center font-bold text-xs shadow flex-shrink-0">4</div>
                                <p class="text-sm font-medium text-muted flex-1 rtl:text-right ltr:text-left">{{ __('Return here and click Verify') }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                </div>

                <p class="text-center text-sm text-muted mt-2 mb-1 flex items-center justify-center gap-1.5 select-none">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    {{ __('Must keep following for at least 30 seconds') }}
                </p>

            </div>
        
