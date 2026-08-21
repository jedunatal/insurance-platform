<div class="space-y-6">

    {{-- Topo: Header com Hero e Botão de Sincronização --}}
    <x-slot:header>
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <x-hero
                badge="Gestão de Retenção"
                title="Esteira de Renovações"
                description="Pipeline ativo de retenção de carteira, cotação e renovação com 1 clique."
                icon="heroicon-o-arrow-path"
            />

            <div>
                <button 
                    type="button" 
                    wire:click="syncExpiringPolicies"
                    wire:loading.attr="disabled"
                    class="inline-flex items-center gap-2 px-4 py-2.5 bg-[#295384] hover:bg-[#1f3f66] text-white text-xs font-semibold rounded-xl shadow-xs transition"
                >
                    <svg wire:loading.remove wire:target="syncExpiringPolicies" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg>
                    <svg wire:loading wire:target="syncExpiringPolicies" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                    <span>Sincronizar Apólices a Vencer</span>
                </button>
            </div>
        </div>
    </x-slot:header>

    {{-- Quadro Kanban de Renovações --}}
    <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-5 gap-4 items-start">
        @foreach($stages as $stage)
            @php
                $stageKey = $stage->value;
                $items = $columns[$stageKey] ?? collect();
                $count = $items->count();

                $badgeColors = match($stageKey) {
                    'to_contact'    => 'border-amber-400 bg-amber-500/10 text-amber-500',
                    'in_quotation'  => 'border-sky-400 bg-sky-500/10 text-sky-500',
                    'proposal_sent' => 'border-indigo-400 bg-indigo-500/10 text-indigo-500',
                    'renewed'       => 'border-emerald-400 bg-emerald-500/10 text-emerald-500',
                    'lost'          => 'border-rose-400 bg-rose-500/10 text-rose-500',
                    default         => 'border-slate-400 bg-slate-500/10 text-slate-500',
                };
            @endphp

            <div class="flex flex-col bg-slate-100/70 dark:bg-slate-900/60 border border-slate-200/80 dark:border-slate-800 rounded-2xl p-3 min-h-[500px]">
                
                {{-- Topo da Coluna --}}
                <div class="flex items-center justify-between pb-3 mb-3 border-b border-slate-200 dark:border-slate-800">
                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full {{ match($stageKey) { 'to_contact' => 'bg-amber-500', 'in_quotation' => 'bg-sky-500', 'proposal_sent' => 'bg-indigo-500', 'renewed' => 'bg-emerald-500', 'lost' => 'bg-rose-500', default => 'bg-slate-400' } }}"></span>
                        <h3 class="text-xs font-bold uppercase tracking-wider text-slate-800 dark:text-slate-200">
                            {{ $stage->getLabel() }}
                        </h3>
                    </div>
                    <span class="px-2 py-0.5 text-[10px] font-extrabold rounded-full border {{ $badgeColors }}">
                        {{ $count }}
                    </span>
                </div>

                {{-- Lista de Cards --}}
                <div class="space-y-3 flex-1 overflow-y-auto max-h-[70vh] pr-1">
                    @forelse($items as $renewal)
                        @php
                            $pol = $renewal->policy;
                            $daysRemaining = $renewal->target_date ? (int) today()->diffInDays($renewal->target_date, false) : null;
                        @endphp

                        <div class="bg-white dark:bg-slate-950 p-3.5 rounded-xl border border-slate-200/80 dark:border-slate-800 shadow-xs hover:shadow-md transition">
                            
                            {{-- Cabeçalho do Card --}}
                            <div class="flex items-start justify-between gap-2">
                                <div>
                                    <h4 class="text-xs font-bold text-slate-900 dark:text-white line-clamp-1">
                                        {{ $pol?->insured?->name ?? ($renewal->insured?->name ?? 'Segurado') }}
                                    </h4>
                                    <p class="text-[11px] text-slate-500 dark:text-slate-400">
                                        {{ $pol?->insurer ?? 'Seguradora N/I' }} • {{ $pol?->branch ?? 'Ramo N/I' }}
                                    </p>
                                </div>
                                <span class="text-[10px] font-mono font-bold text-[#295384] dark:text-blue-400 shrink-0">
                                    #{{ $pol?->policy_number }}
                                </span>
                            </div>

                            {{-- Objeto Segurado ou Dados Financeiros --}}
                            <div class="mt-2.5 pt-2 border-t border-slate-100 dark:border-slate-800/80 text-[11px] space-y-1">
                                <div class="flex justify-between text-slate-600 dark:text-slate-300">
                                    <span>Prêmio Vigente:</span>
                                    <strong class="text-slate-900 dark:text-white">R$ {{ number_format($pol?->total_premium ?? 0, 2, ',', '.') }}</strong>
                                </div>
                                <div class="flex justify-between items-center text-[10px]">
                                    <span class="text-slate-500">Vencimento:</span>
                                    @if($daysRemaining !== null)
                                        <span class="font-semibold {{ $daysRemaining <= 7 ? 'text-rose-600 dark:text-rose-400 font-bold' : ($daysRemaining <= 20 ? 'text-amber-600 dark:text-amber-400' : 'text-slate-600 dark:text-slate-300') }}">
                                            {{ $renewal->target_date->format('d/m/Y') }} 
                                            @if($daysRemaining < 0)
                                                (Vencido há {{ abs($daysRemaining) }}d)
                                            @else
                                                (em {{ $daysRemaining }}d)
                                            @endif
                                        </span>
                                    @else
                                        <span class="text-slate-400">-</span>
                                    @endif
                                </div>
                            </div>

                            @if($stageKey === 'lost' && $renewal->loss_reason)
                                <div class="mt-2 p-1.5 bg-rose-50 dark:bg-rose-500/10 rounded text-[10px] text-rose-700 dark:text-rose-300">
                                    <strong>Motivo:</strong> {{ $renewal->loss_reason?->getLabel() ?? $renewal->loss_reason }}
                                </div>
                            @endif

                            {{-- Barra de Ações Rápidas do Card --}}
                            <div class="mt-3 pt-2 border-t border-slate-100 dark:border-slate-800/80 flex flex-wrap items-center justify-between gap-1">
                                
                                @if($pol)
                                    <a 
                                        href="{{ route('policies.view', $pol) }}" 
                                        class="text-[10px] text-slate-500 hover:text-[#295384] dark:hover:text-blue-400 font-medium"
                                        title="Ver Apólice"
                                    >
                                        Ver Apólice
                                    </a>
                                @endif

                                <div class="flex items-center gap-1">
                                    {{-- Botão de Renovar em 1 Clique (se não for renovada ainda) --}}
                                    @if($stageKey !== 'renewed' && $stageKey !== 'lost')
                                        <button 
                                            type="button" 
                                            wire:click="renewInOneClick({{ $renewal->id }})"
                                            wire:confirm="Deseja emitir a renovação da apólice #{{ $pol?->policy_number }} para o próximo período?"
                                            class="px-2 py-1 bg-emerald-600 hover:bg-emerald-700 text-white rounded text-[10px] font-bold shadow-xs transition"
                                            title="Emitir Renovação"
                                        >
                                            ⚡ Renovar 1-Clique
                                        </button>
                                    @endif

                                    {{-- Dropdown de Movimentação de Estágio --}}
                                    <div x-data="{ openMenu: false }" class="relative">
                                        <button 
                                            @click="openMenu = !openMenu" 
                                            type="button" 
                                            class="p-1 rounded text-slate-400 hover:text-slate-600 dark:hover:text-white transition"
                                            title="Alterar Estágio"
                                        >
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z" /></svg>
                                        </button>
                                        
                                        <div 
                                            x-show="openMenu" 
                                            @click.outside="openMenu = false"
                                            x-cloak
                                            class="absolute right-0 bottom-full mb-1 w-44 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl shadow-xl z-20 p-1 text-[11px]"
                                        >
                                            <span class="block px-2 py-1 text-[9px] font-bold uppercase tracking-wider text-slate-400">Mover para:</span>
                                            @foreach($stages as $stg)
                                                @if($stg->value !== $stageKey)
                                                    <button 
                                                        type="button" 
                                                        wire:click="moveStage({{ $renewal->id }}, '{{ $stg->value }}')"
                                                        @click="openMenu = false"
                                                        class="w-full text-left px-2 py-1.5 rounded hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-300 font-medium transition"
                                                    >
                                                        {{ $stg->getLabel() }}
                                                    </button>
                                                @endif
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    @empty
                        <div class="py-8 text-center text-xs text-slate-400">
                            Nenhuma apólice neste estágio.
                        </div>
                    @endforelse
                </div>

            </div>
        @endforeach
    </div>

    {{-- Modal de Registro de Perda / Não Renovação --}}
    @if($lossModalOpen)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs" x-cloak>
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl shadow-2xl max-w-md w-full p-6 space-y-4">
                <div class="flex items-center justify-between">
                    <h3 class="text-sm font-bold text-slate-900 dark:text-white">Registrar Perda / Não Renovação</h3>
                    <button type="button" wire:click="$set('lossModalOpen', false)" class="text-slate-400 hover:text-slate-600">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>

                <div class="space-y-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Motivo da Não Renovação</label>
                        <select wire:model="lossReason" class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-700 rounded-xl px-3 py-2 text-xs text-slate-900 dark:text-white focus:outline-none focus:border-[#295384]">
                            @foreach(\App\Enums\RenewalLossReasonEnum::options() as $val => $lbl)
                                <option value="{{ $val }}">{{ $lbl }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Observações do Atendimento</label>
                        <textarea wire:model="lossNotes" rows="3" placeholder="Detalhes da recusa do cliente ou cotação concorrente..." class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-700 rounded-xl px-3 py-2 text-xs text-slate-900 dark:text-white focus:outline-none focus:border-[#295384]"></textarea>
                    </div>
                </div>

                <div class="flex justify-end gap-2 pt-2 border-t border-slate-100 dark:border-slate-800">
                    <button type="button" wire:click="$set('lossModalOpen', false)" class="px-4 py-2 border border-slate-300 dark:border-slate-700 text-slate-700 dark:text-slate-300 text-xs font-medium rounded-xl">Cancelar</button>
                    <button type="button" wire:click="confirmLoss" class="px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white text-xs font-semibold rounded-xl">Confirmar Perda</button>
                </div>
            </div>
        </div>
    @endif

</div>
