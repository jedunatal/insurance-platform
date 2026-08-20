<div 
    class="relative w-64 sm:w-80 md:w-96" 
    x-data="{ 
        open: @entangle('isOpen'),
        focusSearch() {
            this.$refs.searchInput.focus();
            this.open = true;
        }
    }"
    @keydown.window.prevent.ctrl.k="focusSearch()"
    @keydown.window.prevent.cmd.k="focusSearch()"
    @keydown.escape.window="open = false; $refs.searchInput.blur()"
    @click.outside="open = false"
>
    {{-- Campo de Busca --}}
    <div class="relative">
        {{-- Ícone da Busca ou Spinner de Loading --}}
        <div class="absolute left-3.5 top-1/2 -translate-y-1/2 flex items-center pointer-events-none text-slate-400 dark:text-slate-500">
            <svg wire:loading.remove wire:target="query" class="w-4 h-4" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 100 11 5.5 5.5 0 000-11zM2 9a7 7 0 1112.452 4.391l3.328 3.329a.75.75 0 11-1.06 1.06l-3.329-3.328A7 7 0 012 9z" clip-rule="evenodd" />
            </svg>
            <svg wire:loading wire:target="query" class="w-4 h-4 animate-spin text-[#295384] dark:text-blue-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
        </div>

        <input 
            type="text"
            x-ref="searchInput"
            wire:model.live.debounce.300ms="query"
            @focus="if ($wire.query.length >= 2) open = true"
            placeholder="Buscar por cliente, CPF, apólice ou sinistro..."
            class="w-full pl-9 pr-14 py-1.5 bg-slate-100 dark:bg-slate-950/60 border border-slate-200/90 dark:border-slate-800/80 rounded-xl text-xs text-slate-900 dark:text-white placeholder:text-slate-400 dark:placeholder:text-slate-500 focus:outline-none focus:border-[#295384] focus:ring-1 focus:ring-[#295384] transition"
        >

        {{-- Atalho de Teclado & Botão Limpar --}}
        <div class="absolute right-2.5 top-1/2 -translate-y-1/2 flex items-center gap-1.5">
            @if(strlen($query) > 0)
                <button 
                    type="button" 
                    wire:click="clearSearch"
                    class="p-0.5 rounded text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 transition"
                    title="Limpar busca"
                >
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            @endif

            <kbd class="hidden sm:inline-flex items-center px-1.5 py-0.5 text-[9px] font-bold text-slate-400 dark:text-slate-500 bg-slate-200/60 dark:bg-slate-800/80 rounded border border-slate-300/80 dark:border-slate-700/80 shadow-xs pointer-events-none">
                ⌘K
            </kbd>
        </div>
    </div>

    {{-- Dropdown Flutuante de Resultados --}}
    <div 
        x-cloak 
        x-show="open && $wire.query.length >= 2" 
        x-transition:enter="transition ease-out duration-150"
        x-transition:enter-start="opacity-0 translate-y-1 scale-98"
        x-transition:enter-end="opacity-100 translate-y-0 scale-100"
        x-transition:leave="transition ease-in duration-100"
        x-transition:leave-start="opacity-100 translate-y-0 scale-100"
        x-transition:leave-end="opacity-0 translate-y-1 scale-98"
        class="absolute left-0 sm:-left-12 sm:w-[500px] w-full mt-2 z-50 bg-white dark:bg-slate-900 border border-slate-200/90 dark:border-slate-800 rounded-2xl shadow-2xl overflow-hidden max-h-[75vh] flex flex-col"
    >
        @php
            $results = $searchResults;
            $hasResults = $results['totalCount'] > 0;
        @endphp

        {{-- Cabeçalho do Dropdown --}}
        <div class="px-4 py-2.5 bg-slate-50/80 dark:bg-slate-800/50 border-b border-slate-100 dark:border-slate-800/80 flex items-center justify-between text-xs text-slate-500 dark:text-slate-400">
            <span class="font-medium">
                @if($hasResults)
                    Resultados para "<strong class="text-slate-900 dark:text-white">{{ $query }}</strong>"
                @else
                    Buscando por "<strong class="text-slate-900 dark:text-white">{{ $query }}</strong>"
                @endif
            </span>
            <span class="text-[10px] font-semibold uppercase tracking-wider text-slate-400">
                {{ $results['totalCount'] }} encontrado(s)
            </span>
        </div>

        {{-- Corpo de Resultados com Scroll --}}
        <div class="overflow-y-auto divide-y divide-slate-100 dark:divide-slate-800/60 p-2 space-y-2">
            
            @if($hasResults)

                {{-- 1. Clientes em Potencial (Leads) --}}
                @if($results['leads']->isNotEmpty())
                    <div>
                        <div class="px-3 py-1.5 text-[10px] font-bold uppercase tracking-wider text-blue-600 dark:text-blue-400 flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 7.5v3m0 0v3m0-3h3m-3 0h-3m-2.25-4.125a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0ZM3 19.235v-.11a6.375 6.375 0 0 1 12.75 0v.109A12.318 12.318 0 0 1 9.374 21c-2.331 0-4.512-.645-6.374-1.766Z"/></svg>
                            Clientes em Potencial (Leads)
                        </div>
                        <div class="space-y-1">
                            @foreach($results['leads'] as $lead)
                                <a 
                                    href="{{ route('leads.view', $lead) }}" 
                                    wire:navigate
                                    class="group flex items-center justify-between px-3 py-2 rounded-xl hover:bg-slate-100 dark:hover:bg-slate-800/60 transition"
                                >
                                    <div class="min-w-0 pr-2">
                                        <p class="text-xs font-bold text-slate-900 dark:text-white truncate group-hover:text-[#295384] dark:group-hover:text-blue-400">
                                            {{ $lead->name }}
                                        </p>
                                        <p class="text-[11px] text-slate-500 dark:text-slate-400 truncate">
                                            {{ $lead->email ?? $lead->phone ?? 'Sem contato' }}
                                        </p>
                                    </div>
                                    <div class="shrink-0 text-right">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold bg-blue-50 dark:bg-blue-500/10 text-blue-700 dark:text-blue-300">
                                            {{ $lead->status instanceof \App\Enums\LeadStatusEnum ? $lead->status->getLabel() : ($lead->status ?? 'Lead') }}
                                        </span>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- 2. Segurados --}}
                @if($results['insureds']->isNotEmpty())
                    <div>
                        <div class="px-3 py-1.5 text-[10px] font-bold uppercase tracking-wider text-emerald-600 dark:text-emerald-400 flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.982 18.725A7.488 7.488 0 0 0 12 15.75a7.488 7.488 0 0 0-5.982 2.975m11.963 0a9 9 0 1 0-11.963 0m11.963 0A8.966 8.966 0 0 1 12 21a8.966 8.966 0 0 1-5.982-2.275M15 9.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/></svg>
                            Segurados Cadastrados
                        </div>
                        <div class="space-y-1">
                            @foreach($results['insureds'] as $insured)
                                <a 
                                    href="{{ route('insureds.view', $insured) }}" 
                                    wire:navigate
                                    class="group flex items-center justify-between px-3 py-2 rounded-xl hover:bg-slate-100 dark:hover:bg-slate-800/60 transition"
                                >
                                    <div class="min-w-0 pr-2">
                                        <p class="text-xs font-bold text-slate-900 dark:text-white truncate group-hover:text-emerald-600 dark:group-hover:text-emerald-400">
                                            {{ $insured->name }}
                                        </p>
                                        <p class="text-[11px] text-slate-500 dark:text-slate-400 truncate">
                                            Doc: {{ $insured->document ?? 'N/I' }} @if($insured->city) • {{ $insured->city }}/{{ $insured->state }} @endif
                                        </p>
                                    </div>
                                    <div class="shrink-0 text-right">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold bg-emerald-50 dark:bg-emerald-500/10 text-emerald-700 dark:text-emerald-300">
                                            Cliente
                                        </span>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- 3. Apólices --}}
                @if($results['policies']->isNotEmpty())
                    <div>
                        <div class="px-3 py-1.5 text-[10px] font-bold uppercase tracking-wider text-amber-600 dark:text-[#B99B6C] flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m18.375 12.739-7.693 7.693a4.5 4.5 0 0 1-6.364-6.364l10.94-10.94A3 3 0 1 1 19.5 7.372L8.552 18.32m.009-.01-.01.01m5.699-9.941-7.81 7.81a1.5 1.5 0 0 0 2.112 2.13"/></svg>
                            Apólices de Seguros
                        </div>
                        <div class="space-y-1">
                            @foreach($results['policies'] as $policy)
                                <a 
                                    href="{{ route('policies.view', $policy) }}" 
                                    wire:navigate
                                    class="group flex items-center justify-between px-3 py-2 rounded-xl hover:bg-slate-100 dark:hover:bg-slate-800/60 transition"
                                >
                                    <div class="min-w-0 pr-2">
                                        <p class="text-xs font-bold text-slate-900 dark:text-white truncate group-hover:text-amber-600 dark:group-hover:text-[#B99B6C]">
                                            Apólice #{{ $policy->policy_number }}
                                        </p>
                                        <p class="text-[11px] text-slate-500 dark:text-slate-400 truncate">
                                            {{ $policy->insured?->name ?? 'Segurado N/I' }} @if($policy->insurer) • {{ $policy->insurer }} @endif
                                        </p>
                                    </div>
                                    <div class="shrink-0 text-right">
                                        <span class="text-xs font-bold text-slate-900 dark:text-white block">
                                            R$ {{ number_format($policy->total_premium, 2, ',', '.') }}
                                        </span>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- 4. Sinistros --}}
                @if($results['claims']->isNotEmpty())
                    <div>
                        <div class="px-3 py-1.5 text-[10px] font-bold uppercase tracking-wider text-rose-600 dark:text-rose-400 flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/></svg>
                            Avisos de Sinistro
                        </div>
                        <div class="space-y-1">
                            @foreach($results['claims'] as $claim)
                                <a 
                                    href="{{ route('claims.view', $claim) }}" 
                                    wire:navigate
                                    class="group flex items-center justify-between px-3 py-2 rounded-xl hover:bg-slate-100 dark:hover:bg-slate-800/60 transition"
                                >
                                    <div class="min-w-0 pr-2">
                                        <p class="text-xs font-bold text-slate-900 dark:text-white truncate group-hover:text-rose-600 dark:group-hover:text-rose-400">
                                            Sinistro #{{ $claim->claim_number ?? 'S/N' }}
                                        </p>
                                        <p class="text-[11px] text-slate-500 dark:text-slate-400 truncate">
                                            {{ $claim->insured?->name ?? 'Segurado N/I' }} @if($claim->protocol_number) • Prot: {{ $claim->protocol_number }} @endif
                                        </p>
                                    </div>
                                    <div class="shrink-0 text-right">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold bg-rose-50 dark:bg-rose-500/10 text-rose-700 dark:text-rose-300">
                                            {{ $claim->status instanceof \App\Enums\ClaimStatusEnum ? $claim->status->getLabel() : ($claim->status ?? 'Sinistro') }}
                                        </span>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif

            @else

                {{-- Estado Vazio --}}
                <div class="py-8 px-4 text-center">
                    <div class="w-10 h-10 mx-auto mb-3 rounded-xl bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-slate-400">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </div>
                    <p class="text-xs font-bold text-slate-800 dark:text-slate-200">
                        Nenhum resultado encontrado para "{{ $query }}"
                    </p>
                    <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1 max-w-xs mx-auto">
                        Tente buscar por nome do cliente, CPF/CNPJ, placa, seguradora, número da apólice ou protocolo de sinistro.
                    </p>
                </div>

            @endif

        </div>

        {{-- Rodapé com Dicas de Atalho --}}
        <div class="px-4 py-2 bg-slate-50/60 dark:bg-slate-800/40 border-t border-slate-100 dark:border-slate-800/80 flex items-center justify-between text-[11px] text-slate-400">
            <span>Pressione <kbd class="px-1 py-0.5 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded text-[10px] font-semibold text-slate-600 dark:text-slate-300">ESC</kbd> para fechar</span>
            <span class="hidden sm:inline">Use <kbd class="px-1 py-0.5 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded text-[10px] font-semibold text-slate-600 dark:text-slate-300">Tab</kbd> para navegar</span>
        </div>
    </div>
</div>
