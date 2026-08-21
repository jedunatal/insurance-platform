<div class="relative" x-data="{ open: @entangle('isOpen') }" @click.outside="open = false">
    {{-- Botão do Sino --}}
    <button 
        type="button" 
        @click="open = !open" 
        class="relative p-2 rounded-xl text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800/60 transition" 
        title="Central de Alertas e Notificações"
    >
        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" />
        </svg>

        @if($totalUnread > 0)
            <span class="absolute top-1.5 right-1.5 flex h-4 w-4 items-center justify-center rounded-full bg-rose-500 text-[10px] font-extrabold text-white animate-pulse">
                {{ $totalUnread > 99 ? '99+' : $totalUnread }}
            </span>
        @endif
    </button>

    {{-- Dropdown de Alertas --}}
    <div 
        x-cloak 
        x-show="open" 
        x-transition:enter="transition ease-out duration-150"
        x-transition:enter-start="opacity-0 translate-y-1 scale-98"
        x-transition:enter-end="opacity-100 translate-y-0 scale-100"
        x-transition:leave="transition ease-in duration-100"
        x-transition:leave-start="opacity-100 translate-y-0 scale-100"
        x-transition:leave-end="opacity-0 translate-y-1 scale-98"
        class="absolute right-0 mt-2 w-80 sm:w-96 z-50 bg-white dark:bg-slate-900 border border-slate-200/90 dark:border-slate-800 rounded-2xl shadow-2xl overflow-hidden"
    >
        {{-- Cabeçalho --}}
        <div class="px-4 py-3 bg-slate-50/90 dark:bg-slate-800/60 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-rose-500"></span>
                <h4 class="text-xs font-bold uppercase tracking-wider text-slate-900 dark:text-white">
                    Alertas Operacionais
                </h4>
            </div>
            <span class="text-[10px] font-semibold text-slate-400">
                {{ $totalUnread }} pendência(s)
            </span>
        </div>

        {{-- Lista de Alertas --}}
        <div class="max-h-80 overflow-y-auto divide-y divide-slate-100 dark:divide-slate-800/60">
            @forelse($alerts as $alert)
                <a 
                    href="{{ $alert['url'] }}" 
                    @click="open = false" 
                    class="p-3.5 flex items-start gap-3 hover:bg-slate-50 dark:hover:bg-slate-800/50 transition block group"
                >
                    <div class="p-2 rounded-xl {{ match($alert['color']) { 'danger' => 'bg-rose-50 text-rose-600 dark:bg-rose-500/10 dark:text-rose-400', 'warning' => 'bg-amber-50 text-amber-600 dark:bg-amber-500/10 dark:text-amber-400', 'primary' => 'bg-blue-50 text-[#295384] dark:bg-blue-500/10 dark:text-blue-400', 'amber' => 'bg-amber-50 text-amber-600 dark:bg-amber-500/10 dark:text-amber-400', default => 'bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-300' } }} shrink-0">
                        @if($alert['type'] === 'expiring_policies')
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        @elseif($alert['type'] === 'overdue_installments')
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                        @elseif($alert['type'] === 'pending_renewals')
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg>
                        @elseif($alert['type'] === 'open_claims')
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" /></svg>
                        @else
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" /></svg>
                        @endif
                    </div>

                    <div class="min-w-0 flex-1">
                        <div class="flex items-center justify-between">
                            <h5 class="text-xs font-bold text-slate-900 dark:text-white group-hover:text-[#295384] dark:group-hover:text-blue-400 transition">
                                {{ $alert['title'] }}
                            </h5>
                            <span class="text-[10px] font-extrabold px-1.5 py-0.2 rounded bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300">
                                {{ $alert['count'] }}
                            </span>
                        </div>
                        <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5">
                            {{ $alert['description'] }}
                        </p>
                    </div>
                </a>
            @empty
                <div class="py-8 px-4 text-center">
                    <div class="w-9 h-9 mx-auto mb-2 rounded-xl bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                    </div>
                    <p class="text-xs font-bold text-slate-800 dark:text-slate-200">
                        Nenhuma pendência urgente!
                    </p>
                    <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5">
                        Sua carteira de apólices e cobranças está 100% em dia.
                    </p>
                </div>
            @endforelse
        </div>
    </div>
</div>
