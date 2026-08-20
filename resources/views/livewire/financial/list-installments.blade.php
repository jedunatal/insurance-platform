<div class="space-y-6">

    {{-- Topo: Header com Hero --}}
    <x-slot:header>
        <x-hero
            badge="Módulo Financeiro"
            title="Gestão Financeira & Comissões"
            description="Controle de faturamento de apólices, grade de parcelas e liquidação de comissões."
            icon="heroicon-o-banknotes"
        />
    </x-slot:header>

    {{-- Cards de Indicadores Financeiros --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        
        {{-- Comissões Previstas / A Receber --}}
        <x-card>
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Comissões a Receber</span>
                <span class="p-2.5 rounded-xl bg-blue-50 text-[#295384] dark:bg-[#295384]/20 dark:text-blue-300">
                    <x-heroicon-m-currency-dollar class="w-5 h-5" />
                </span>
            </div>
            <div class="mt-3">
                <p class="text-2xl font-extrabold text-slate-900 dark:text-white">
                    R$ {{ number_format($metrics['expected_commissions'], 2, ',', '.') }}
                </p>
                <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Projetado em parcelas pendentes</p>
            </div>
        </x-card>

        {{-- Comissões Efetivamente Recebidas --}}
        <x-card>
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Comissões Recebidas</span>
                <span class="p-2.5 rounded-xl bg-emerald-50 text-emerald-600 dark:bg-emerald-500/10 dark:text-emerald-400">
                    <x-heroicon-m-check-badge class="w-5 h-5" />
                </span>
            </div>
            <div class="mt-3">
                <p class="text-2xl font-extrabold text-emerald-600 dark:text-emerald-400">
                    R$ {{ number_format($metrics['received_commissions'], 2, ',', '.') }}
                </p>
                <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Total liquidado na corretora</p>
            </div>
        </x-card>

        {{-- Prêmios Brutos Pendentes --}}
        <x-card>
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Faturamento a Vencer</span>
                <span class="p-2.5 rounded-xl bg-amber-50 text-[#B99B6C] dark:bg-amber-500/10 dark:text-[#B99B6C]">
                    <x-heroicon-m-calendar-days class="w-5 h-5" />
                </span>
            </div>
            <div class="mt-3">
                <p class="text-2xl font-extrabold text-slate-900 dark:text-white">
                    R$ {{ number_format($metrics['pending_gross'], 2, ',', '.') }}
                </p>
                <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Prêmios em cobrança</p>
            </div>
        </x-card>

        {{-- Inadimplência / Atraso --}}
        <x-card>
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Parcelas em Atraso</span>
                <span class="p-2.5 rounded-xl bg-rose-50 text-rose-600 dark:bg-rose-500/10 dark:text-rose-400">
                    <x-heroicon-m-exclamation-triangle class="w-5 h-5" />
                </span>
            </div>
            <div class="mt-3">
                <p class="text-2xl font-extrabold text-rose-600 dark:text-rose-400">
                    {{ $metrics['overdue_count'] }}
                </p>
                <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Contratos com vencimento expirado</p>
            </div>
        </x-card>

    </div>

    {{-- Tabela Filament de Parcelas e Comissões --}}
    <x-card class="overflow-hidden">
        {{ $this->table }}
    </x-card>

</div>
