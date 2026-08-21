<div class="space-y-6">

    {{-- Topo --}}
    <x-slot:header>
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <x-hero
                badge="Estudo Comparativo"
                title="Cotação #{{ $record->quote_number }}"
                description="Comparativo de seguradoras para {{ $record->insured?->name ?? ($record->lead?->name ?? $record->title) }}."
                icon="heroicon-o-scale"
            />

            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('quotes.index') }}" class="px-4 py-2 border border-slate-300 dark:border-slate-700 text-slate-700 dark:text-slate-300 rounded-xl text-xs font-semibold hover:bg-slate-50 dark:hover:bg-slate-800 transition">
                    Voltar
                </a>
                <a href="{{ route('quotes.document.view', $record) }}" target="_blank" class="px-4 py-2 bg-slate-700 hover:bg-slate-800 text-white rounded-xl text-xs font-semibold transition inline-flex items-center gap-1.5 shadow-xs">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                    Ver Proposta
                </a>
                <a href="{{ route('quotes.document.download', $record) }}" class="px-4 py-2 bg-[#295384] hover:bg-[#1f3f66] text-white rounded-xl text-xs font-semibold transition inline-flex items-center gap-1.5 shadow-xs">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" /></svg>
                    Baixar PDF Comercial
                </a>
            </div>
        </div>
    </x-slot:header>

    {{-- Dados Gerais da Cotação --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <x-card>
            <span class="text-xs font-bold text-slate-500 uppercase tracking-wider block">Proponente</span>
            <p class="text-sm font-bold text-slate-900 dark:text-white mt-1">
                {{ $record->insured?->name ?? ($record->lead?->name ?? 'Não informado') }}
            </p>
            <p class="text-xs text-slate-500 mt-0.5">Ramo: {{ $record->branch?->getLabel() ?? $record->branch }}</p>
        </x-card>

        <x-card>
            <span class="text-xs font-bold text-slate-500 uppercase tracking-wider block">Situação da Proposta</span>
            <div class="mt-1">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold {{ match($record->status?->value ?? $record->status) { 'approved' => 'bg-amber-100 text-amber-800', 'converted' => 'bg-emerald-100 text-emerald-800', 'sent' => 'bg-blue-100 text-blue-800', default => 'bg-slate-100 text-slate-800' } }}">
                    {{ $record->status instanceof \App\Enums\QuoteStatusEnum ? $record->status->getLabel() : ucfirst((string)$record->status) }}
                </span>
            </div>
            <p class="text-xs text-slate-500 mt-1">Validade: {{ $record->valid_until ? $record->valid_until->format('d/m/Y') : '15 dias' }}</p>
        </x-card>

        <x-card>
            <span class="text-xs font-bold text-slate-500 uppercase tracking-wider block">Apólice Emitida</span>
            @if($record->convertedPolicy)
                <a href="{{ route('policies.view', $record->convertedPolicy) }}" class="text-sm font-bold text-[#295384] dark:text-blue-400 hover:underline mt-1 block">
                    Apólice #{{ $record->convertedPolicy->policy_number }}
                </a>
                <p class="text-xs text-emerald-600 dark:text-emerald-400 font-semibold mt-0.5">Contrato Ativo</p>
            @else
                <p class="text-sm font-semibold text-slate-400 mt-1">Aguardando aceite do cliente</p>
            @endif
        </x-card>
    </div>

    {{-- Cards Comparativos Lado a Lado --}}
    <div class="space-y-4">
        <h3 class="text-sm font-bold uppercase tracking-wider text-slate-800 dark:text-slate-200">
            Alternativas Cotadas ({{ $record->options->count() }} Seguradoras)
        </h3>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($record->options as $opt)
                <div class="relative bg-white dark:bg-slate-900 border {{ $opt->is_recommended ? 'border-2 border-emerald-500 shadow-xl' : 'border-slate-200 dark:border-slate-800 shadow-sm' }} rounded-2xl p-6 flex flex-col justify-between transition hover:-translate-y-0.5">
                    
                    @if($opt->is_recommended)
                        <div class="absolute -top-3 left-1/2 -translate-x-1/2 bg-emerald-600 text-white text-[10px] font-extrabold uppercase tracking-widest px-3 py-0.5 rounded-full shadow-xs">
                            Melhor Custo x Benefício
                        </div>
                    @endif

                    <div class="space-y-4">
                        {{-- Topo do Card --}}
                        <div class="border-b border-slate-100 dark:border-slate-800 pb-3">
                            <h4 class="text-base font-extrabold text-slate-900 dark:text-white">
                                {{ $opt->insurer }}
                            </h4>
                            <div class="mt-2 flex items-baseline gap-1">
                                <span class="text-2xl font-black text-[#295384] dark:text-blue-400">
                                    {{ $opt->formattedTotalPremium() }}
                                </span>
                                <span class="text-xs text-slate-500 font-medium">/ ano</span>
                            </div>
                            @if($opt->payment_conditions)
                                <p class="text-xs text-slate-500 mt-0.5">{{ $opt->payment_conditions }}</p>
                            @endif
                        </div>

                        {{-- Especificações --}}
                        <div class="space-y-2 text-xs">
                            <div class="flex justify-between py-1 border-b border-slate-50 dark:border-slate-800/60">
                                <span class="text-slate-500">Franquia:</span>
                                <strong class="text-slate-900 dark:text-white">{{ $opt->formattedDeductibleAmount() }} ({{ ucfirst($opt->deductible_type) }})</strong>
                            </div>
                            <div class="flex justify-between py-1 border-b border-slate-50 dark:border-slate-800/60">
                                <span class="text-slate-500">Carro Reserva:</span>
                                <strong class="text-slate-900 dark:text-white">{{ $opt->car_rental ?? 'Padrão' }}</strong>
                            </div>
                            <div class="flex justify-between py-1 border-b border-slate-50 dark:border-slate-800/60">
                                <span class="text-slate-500">Vidros / Faróis:</span>
                                <strong class="text-slate-900 dark:text-white">{{ $opt->glass_coverage ?? 'Completa' }}</strong>
                            </div>
                            @if($opt->third_party_materials > 0)
                                <div class="flex justify-between py-1 border-b border-slate-50 dark:border-slate-800/60">
                                    <span class="text-slate-500">RCF-V Danos Materiais:</span>
                                    <strong class="text-slate-900 dark:text-white">R$ {{ number_format((float) $opt->third_party_materials, 2, ',', '.') }}</strong>
                                </div>
                            @endif
                        </div>

                        @if($opt->highlights)
                            <div class="p-2.5 bg-slate-50 dark:bg-slate-950 rounded-xl text-xs text-slate-600 dark:text-slate-300">
                                <strong>Diferencial:</strong> {{ $opt->highlights }}
                            </div>
                        @endif
                    </div>

                    {{-- Botão de Aceite / Conversão --}}
                    <div class="mt-6 pt-4 border-t border-slate-100 dark:border-slate-800">
                        @if($opt->is_accepted)
                            <div class="w-full py-2.5 bg-emerald-50 dark:bg-emerald-500/10 text-emerald-700 dark:text-emerald-300 font-bold text-center text-xs rounded-xl flex items-center justify-center gap-1.5">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                                Opção Contratada
                            </div>
                        @elseif($record->status?->value !== 'converted')
                            <button 
                                type="button" 
                                wire:click="acceptOption({{ $opt->id }})"
                                wire:confirm="Confirmar o aceite da opção {{ $opt->insurer }} e emitir a apólice agora?"
                                class="w-full py-2.5 {{ $opt->is_recommended ? 'bg-emerald-600 hover:bg-emerald-700 text-white' : 'bg-[#295384] hover:bg-[#1f3f66] text-white' }} font-bold text-xs rounded-xl shadow-xs transition"
                            >
                                Aceitar Opção & Emitir Apólice
                            </button>
                        @endif
                    </div>

                </div>
            @endforeach
        </div>
    </div>

</div>
