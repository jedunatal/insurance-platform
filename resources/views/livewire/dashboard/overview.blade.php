<div class="space-y-6">

    {{-- Topo: Header com Boas-Vindas e Ações Rápidas --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 pb-2">
        <div>
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-[#295384]/10 dark:bg-[#295384]/20 border border-[#295384]/30 text-[#295384] dark:text-blue-300 text-xs font-semibold mb-2">
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                Painel Operacional em Tempo Real
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-slate-900 dark:text-white">
                Dashboard Executivo
            </h1>
            <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">
                Visão consolidada da carteira de seguros, funil de prospecção, renovações e regulação de sinistros.
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-2.5">
            <a
                href="{{ route('policies.create') }}"
                wire:navigate
                class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-[#295384] hover:bg-[#1f3f64] text-white text-xs font-semibold shadow-sm transition"
            >
                <x-heroicon-m-plus class="w-4 h-4" />
                Nova Apólice
            </a>
            <a
                href="{{ route('claims.create') }}"
                wire:navigate
                class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-amber-600 hover:bg-amber-700 text-white text-xs font-semibold shadow-sm transition"
            >
                <x-heroicon-m-exclamation-triangle class="w-4 h-4" />
                Avisar Sinistro
            </a>
            <a
                href="{{ route('leads.create') }}"
                wire:navigate
                class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-white text-xs font-semibold shadow-sm transition"
            >
                <x-heroicon-m-user-plus class="w-4 h-4" />
                Novo Lead
            </a>
        </div>
    </div>

    {{-- 1. Grid com os 4 Cards Principais de KPIs --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        
        {{-- Card 1: Leads & Taxa de Conversão --}}
        <x-card class="relative overflow-hidden group hover:border-[#295384]/60 transition-all duration-300">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Leads do Mês</span>
                <span class="p-2.5 rounded-xl bg-blue-50 text-[#295384] dark:bg-[#295384]/20 dark:text-blue-300">
                    <x-heroicon-m-funnel class="w-5 h-5" />
                </span>
            </div>
            <div class="mt-4">
                <div class="flex items-baseline justify-between">
                    <p class="text-2xl sm:text-3xl font-extrabold text-slate-900 dark:text-white">
                        {{ number_format($metrics['month_leads'], 0, ',', '.') }}
                    </p>
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-lg text-xs font-bold bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20">
                        {{ $metrics['conversion_rate'] }}% conv.
                    </span>
                </div>
                <div class="mt-3 flex items-center justify-between text-xs text-slate-500 dark:text-slate-400">
                    <span>{{ $metrics['converted_leads'] }} convertidos de {{ $metrics['total_leads'] }} total</span>
                    <a href="{{ route('leads.index') }}" wire:navigate class="text-[#295384] dark:text-blue-400 font-semibold hover:underline">
                        Ver leads &rarr;
                    </a>
                </div>
            </div>
        </x-card>

        {{-- Card 2: Segurados Ativos --}}
        <x-card class="relative overflow-hidden group hover:border-emerald-500/60 transition-all duration-300">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Segurados Ativos</span>
                <span class="p-2.5 rounded-xl bg-emerald-50 text-emerald-600 dark:bg-emerald-500/10 dark:text-emerald-400">
                    <x-heroicon-m-user-group class="w-5 h-5" />
                </span>
            </div>
            <div class="mt-4">
                <div class="flex items-baseline justify-between">
                    <p class="text-2xl sm:text-3xl font-extrabold text-slate-900 dark:text-white">
                        {{ number_format($metrics['active_insureds'], 0, ',', '.') }}
                    </p>
                    <span class="text-xs font-medium text-emerald-600 dark:text-emerald-400">
                        {{ $metrics['insureds_with_policies'] }} c/ apólice
                    </span>
                </div>
                <div class="mt-3 flex items-center justify-between text-xs text-slate-500 dark:text-slate-400">
                    <span>Base cadastral total</span>
                    <a href="{{ route('insureds.index') }}" wire:navigate class="text-emerald-600 dark:text-emerald-400 font-semibold hover:underline">
                        Ver clientes &rarr;
                    </a>
                </div>
            </div>
        </x-card>

        {{-- Card 3: Carteira & Prêmio Total Emitido --}}
        <x-card class="relative overflow-hidden group hover:border-[#B99B6C]/60 transition-all duration-300">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Prêmio Total Ativo</span>
                <span class="p-2.5 rounded-xl bg-amber-50 text-[#B99B6C] dark:bg-amber-500/10 dark:text-[#B99B6C]">
                    <x-heroicon-m-banknotes class="w-5 h-5" />
                </span>
            </div>
            <div class="mt-4">
                <div class="flex items-baseline justify-between">
                    <p class="text-xl sm:text-2xl font-extrabold text-slate-900 dark:text-white truncate">
                        R$ {{ number_format($metrics['total_active_premium'], 2, ',', '.') }}
                    </p>
                </div>
                <div class="mt-3 flex items-center justify-between text-xs text-slate-500 dark:text-slate-400">
                    <span>{{ number_format($metrics['active_policies'], 0, ',', '.') }} apólices ativas</span>
                    <a href="{{ route('policies.index') }}" wire:navigate class="text-[#B99B6C] font-semibold hover:underline">
                        Ver apólices &rarr;
                    </a>
                </div>
            </div>
        </x-card>

        {{-- Card 4: Sinistros em Aberto & Prejuízo Estimado --}}
        <x-card class="relative overflow-hidden group hover:border-rose-500/60 transition-all duration-300">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Sinistros Abertos</span>
                <span class="p-2.5 rounded-xl bg-rose-50 text-rose-600 dark:bg-rose-500/10 dark:text-rose-400">
                    <x-heroicon-m-shield-exclamation class="w-5 h-5" />
                </span>
            </div>
            <div class="mt-4">
                <div class="flex items-baseline justify-between">
                    <p class="text-2xl sm:text-3xl font-extrabold text-slate-900 dark:text-white">
                        {{ number_format($metrics['open_claims'], 0, ',', '.') }}
                    </p>
                    <span class="text-xs font-semibold text-rose-600 dark:text-rose-400">
                        Est: R$ {{ number_format($metrics['estimated_loss'], 2, ',', '.') }}
                    </span>
                </div>
                <div class="mt-3 flex items-center justify-between text-xs text-slate-500 dark:text-slate-400">
                    <span>Sinistralidade: {{ $metrics['loss_ratio'] }}%</span>
                    <a href="{{ route('claims.index') }}" wire:navigate class="text-rose-600 dark:text-rose-400 font-semibold hover:underline">
                        Regulação &rarr;
                    </a>
                </div>
            </div>
        </x-card>

    </div>

    {{-- 2. Seção Central: Alertas de Renovações e Gráficos de Distribuição --}}
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        
        {{-- Card de Alerta/Ação Imediata: Renovações Próximas (30 dias) --}}
        <div class="lg:col-span-6">
            <x-card 
                title="Renovações Próximas (30 Dias)" 
                description="Contratos com vigência expirando nos próximos 30 dias para contato de renovação."
            >
                <x-slot:headerActions>
                    <span class="px-2.5 py-1 text-xs font-bold rounded-lg bg-amber-500/10 text-amber-600 dark:text-[#B99B6C] border border-amber-500/20">
                        {{ $criticalRenewalsCount }} contratos
                    </span>
                </x-slot:headerActions>

                <div class="divide-y divide-slate-100 dark:divide-slate-800/80 -mx-6 -my-4">
                    @forelse($criticalRenewals as $policy)
                        @php
                            $daysLeft = (int) now()->startOfDay()->diffInDays($policy->end_date->startOfDay(), false);
                        @endphp
                        <div class="px-6 py-3.5 flex items-center justify-between hover:bg-slate-50/60 dark:hover:bg-slate-800/40 transition">
                            <div class="min-w-0 pr-4">
                                <div class="flex items-center gap-2">
                                    <p class="text-xs font-bold text-slate-900 dark:text-white truncate">
                                        {{ $policy->insured?->name ?? 'Segurado não informado' }}
                                    </p>
                                    @if($policy->branch)
                                        <span class="px-1.5 py-0.5 rounded text-[10px] font-semibold bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300">
                                            {{ $policy->branch }}
                                        </span>
                                    @endif
                                </div>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5 truncate">
                                    Apólice: <span class="font-medium text-slate-700 dark:text-slate-300">{{ $policy->policy_number }}</span>
                                    @if($policy->insurer)
                                        • {{ $policy->insurer }}
                                    @endif
                                </p>
                            </div>
                            <div class="text-right shrink-0 flex items-center gap-3">
                                <div>
                                    <span class="inline-block text-xs font-extrabold {{ $daysLeft <= 7 ? 'text-rose-600 dark:text-rose-400' : 'text-amber-600 dark:text-[#B99B6C]' }}">
                                        @if($daysLeft === 0)
                                            Vence hoje!
                                        @elseif($daysLeft === 1)
                                            Vence amanhã
                                        @else
                                            Em {{ $daysLeft }} dias
                                        @endif
                                    </span>
                                    <p class="text-[11px] text-slate-500 dark:text-slate-400 font-medium">
                                        R$ {{ number_format($policy->total_premium, 2, ',', '.') }}
                                    </p>
                                </div>
                                <a
                                    href="{{ route('policies.view', $policy) }}"
                                    wire:navigate
                                    class="p-1.5 rounded-lg bg-slate-100 dark:bg-slate-800 hover:bg-[#295384] hover:text-white text-slate-600 dark:text-slate-300 transition"
                                    title="Visualizar Apólice"
                                >
                                    <x-heroicon-m-arrow-right class="w-4 h-4" />
                                </a>
                            </div>
                        </div>
                    @empty
                        <div class="px-6 py-10 text-center text-xs text-slate-400">
                            <x-heroicon-o-shield-check class="w-8 h-8 mx-auto text-emerald-500/60 mb-2" />
                            Nenhuma apólice com renovação crítica prevista para os próximos 30 dias.
                        </div>
                    @endforelse
                </div>

                <x-slot:footer>
                    <div class="flex items-center justify-between text-xs">
                        <span class="text-slate-400">Exibindo até 6 renovações imediatas</span>
                        <a href="{{ route('policies.index') }}" wire:navigate class="font-semibold text-[#295384] dark:text-blue-400 hover:underline">
                            Ver todas as apólices &rarr;
                        </a>
                    </div>
                </x-slot:footer>
            </x-card>
        </div>

        {{-- Métricas de Distribuição: Ramos de Seguro & Seguradoras --}}
        <div class="lg:col-span-6 space-y-6">
            
            {{-- Distribuição por Ramos de Seguro --}}
            <x-card title="Distribuição por Ramos" description="Participação dos ramos na carteira de seguros ativa.">
                <div class="space-y-3.5">
                    @forelse($branchDistribution as $branch)
                        <div>
                            <div class="flex items-center justify-between text-xs font-semibold mb-1">
                                <span class="text-slate-800 dark:text-slate-200">{{ $branch['label'] }}</span>
                                <div class="flex items-center gap-2">
                                    <span class="text-slate-500 dark:text-slate-400 font-normal">{{ $branch['count'] }} contratos (R$ {{ number_format($branch['total_premium'], 2, ',', '.') }})</span>
                                    <span class="text-[#295384] dark:text-blue-400 font-bold">{{ $branch['percentage'] }}%</span>
                                </div>
                            </div>
                            <div class="w-full h-2 bg-slate-100 dark:bg-slate-800 rounded-full overflow-hidden">
                                <div class="h-full bg-gradient-to-r from-[#295384] to-blue-500 rounded-full transition-all duration-500" style="width: {{ $branch['percentage'] }}%"></div>
                            </div>
                        </div>
                    @empty
                        <p class="text-xs text-slate-400 text-center py-4">Nenhum dado de ramos disponível.</p>
                    @endforelse
                </div>
            </x-card>

            {{-- Distribuição por Seguradora & Funil de Leads --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                
                {{-- Top Seguradoras --}}
                <x-card title="Top Seguradoras" class="!p-4">
                    <div class="space-y-2.5 mt-2">
                        @forelse($insurerDistribution as $insurer)
                            <div class="flex items-center justify-between text-xs">
                                <div class="flex items-center gap-2 truncate">
                                    <span class="w-2 h-2 rounded-full bg-[#B99B6C]"></span>
                                    <span class="font-medium text-slate-800 dark:text-slate-200 truncate">{{ $insurer['insurer'] }}</span>
                                </div>
                                <span class="font-bold text-slate-700 dark:text-slate-300 shrink-0">{{ $insurer['count'] }} ({{ $insurer['percentage'] }}%)</span>
                            </div>
                        @empty
                            <p class="text-xs text-slate-400 text-center py-2">Sem emissões.</p>
                        @endforelse
                    </div>
                </x-card>

                {{-- Funil de Leads --}}
                <x-card title="Funil de Leads" class="!p-4">
                    <div class="space-y-2 mt-2">
                        @forelse($leadFunnel as $step)
                            <div class="flex items-center justify-between text-xs">
                                <span class="text-slate-600 dark:text-slate-400">{{ $step['label'] }}</span>
                                <div class="flex items-center gap-1.5">
                                    <span class="font-bold text-slate-800 dark:text-slate-200">{{ $step['count'] }}</span>
                                    <span class="text-[10px] text-slate-400">({{ $step['percentage'] }}%)</span>
                                </div>
                            </div>
                        @empty
                            <p class="text-xs text-slate-400 text-center py-2">Sem leads.</p>
                        @endforelse
                    </div>
                </x-card>

            </div>

        </div>

    </div>

    {{-- 3. Base: Duas Tabelas / Feeds Rápidos Lado a Lado --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        
        {{-- Tabela 1: Últimas Apólices Emitidas --}}
        <x-card title="Últimas Apólices Emitidas" description="Contratos mais recentes cadastrados na plataforma.">
            <x-slot:headerActions>
                <a href="{{ route('policies.index') }}" wire:navigate class="text-xs font-semibold text-[#295384] dark:text-blue-400 hover:underline">
                    Ver todas &rarr;
                </a>
            </x-slot:headerActions>

            <div class="overflow-x-auto -mx-6 -my-4">
                <table class="w-full text-left text-xs text-slate-600 dark:text-slate-300">
                    <thead class="bg-slate-50/80 dark:bg-slate-800/50 text-[10px] uppercase font-bold text-slate-400 border-b border-slate-100 dark:border-slate-800">
                        <tr>
                            <th class="px-6 py-3">Apólice / Seguradora</th>
                            <th class="px-4 py-3">Segurado</th>
                            <th class="px-4 py-3">Ramo</th>
                            <th class="px-4 py-3 text-right">Prêmio</th>
                            <th class="px-4 py-3 text-center">Status</th>
                            <th class="px-6 py-3 text-right">Ação</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800/60">
                        @forelse($recentPolicies as $policy)
                            <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-800/30 transition">
                                <td class="px-6 py-3 font-semibold text-slate-900 dark:text-white whitespace-nowrap">
                                    {{ $policy->policy_number }}
                                    <p class="text-[10px] text-slate-400 font-normal">{{ $policy->insurer ?? 'Seguradora N/I' }}</p>
                                </td>
                                <td class="px-4 py-3 font-medium truncate max-w-[140px]">
                                    {{ $policy->insured?->name ?? '-' }}
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-medium bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300">
                                        {{ $policy->branch ?? '-' }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-right font-bold text-slate-900 dark:text-white whitespace-nowrap">
                                    R$ {{ number_format($policy->total_premium, 2, ',', '.') }}
                                </td>
                                <td class="px-4 py-3 text-center whitespace-nowrap">
                                    @php
                                        $statusClass = match($policy->status?->value ?? (string)$policy->status) {
                                            'active', 'Active', 'Ativa' => 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border-emerald-500/20',
                                            'draft', 'Draft' => 'bg-slate-500/10 text-slate-600 dark:text-slate-400 border-slate-500/20',
                                            'cancelled', 'Cancelled' => 'bg-rose-500/10 text-rose-600 dark:text-rose-400 border-rose-500/20',
                                            default => 'bg-blue-500/10 text-[#295384] dark:text-blue-300 border-blue-500/20',
                                        };
                                        $statusLabel = $policy->status instanceof \App\Enums\PolicyStatusEnum 
                                            ? $policy->status->getLabel() 
                                            : ucfirst((string)$policy->status);
                                    @endphp
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold border {{ $statusClass }}">
                                        {{ $statusLabel }}
                                    </span>
                                </td>
                                <td class="px-6 py-3 text-right whitespace-nowrap">
                                    <a href="{{ route('policies.view', $policy) }}" wire:navigate class="p-1 rounded text-slate-400 hover:text-[#295384] dark:hover:text-blue-300 transition">
                                        <x-heroicon-m-eye class="w-4 h-4 inline" />
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-6 text-center text-xs text-slate-400">
                                    Nenhuma apólice registrada recentemente.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-card>

        {{-- Tabela 2: Sinistros Recentes --}}
        <x-card title="Sinistros Recentes" description="Ocorrências registradas aguardando regulação ou liquidação.">
            <x-slot:headerActions>
                <a href="{{ route('claims.index') }}" wire:navigate class="text-xs font-semibold text-rose-600 dark:text-rose-400 hover:underline">
                    Ver todos &rarr;
                </a>
            </x-slot:headerActions>

            <div class="overflow-x-auto -mx-6 -my-4">
                <table class="w-full text-left text-xs text-slate-600 dark:text-slate-300">
                    <thead class="bg-slate-50/80 dark:bg-slate-800/50 text-[10px] uppercase font-bold text-slate-400 border-b border-slate-100 dark:border-slate-800">
                        <tr>
                            <th class="px-6 py-3">Sinistro / Protocolo</th>
                            <th class="px-4 py-3">Segurado</th>
                            <th class="px-4 py-3">Tipo</th>
                            <th class="px-4 py-3">Data Evento</th>
                            <th class="px-4 py-3 text-center">Status</th>
                            <th class="px-6 py-3 text-right">Ação</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800/60">
                        @forelse($recentClaims as $claim)
                            <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-800/30 transition">
                                <td class="px-6 py-3 font-semibold text-slate-900 dark:text-white whitespace-nowrap">
                                    {{ $claim->claim_number ?? 'S/N' }}
                                    <p class="text-[10px] text-slate-400 font-normal">{{ $claim->protocol_number ? 'Prot: ' . $claim->protocol_number : 'Sem protocolo' }}</p>
                                </td>
                                <td class="px-4 py-3 font-medium truncate max-w-[140px]">
                                    {{ $claim->insured?->name ?? '-' }}
                                    <p class="text-[10px] text-slate-400 font-normal">Ap: {{ $claim->policy?->policy_number ?? '-' }}</p>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-medium bg-rose-500/10 text-rose-600 dark:text-rose-400 border border-rose-500/20">
                                        {{ $claim->claim_type instanceof \App\Enums\ClaimTypeEnum ? $claim->claim_type->getLabel() : ($claim->claim_type ?? 'Geral') }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-slate-500 dark:text-slate-400">
                                    {{ $claim->occurrence_date ? $claim->occurrence_date->format('d/m/Y H:i') : '-' }}
                                </td>
                                <td class="px-4 py-3 text-center whitespace-nowrap">
                                    @php
                                        $claimStatusClass = match($claim->status?->value ?? (string)$claim->status) {
                                            'reported', 'Reported', 'Aberto' => 'bg-amber-500/10 text-amber-600 dark:text-[#B99B6C] border-amber-500/20',
                                            'under_review', 'UnderReview', 'Em Análise' => 'bg-blue-500/10 text-blue-600 dark:text-blue-400 border-blue-500/20',
                                            'indemnified', 'Indemnified', 'Indenizado' => 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border-emerald-500/20',
                                            'rejected', 'Rejected', 'Recusado' => 'bg-rose-500/10 text-rose-600 dark:text-rose-400 border-rose-500/20',
                                            default => 'bg-slate-500/10 text-slate-600 dark:text-slate-400 border-slate-500/20',
                                        };
                                        $claimStatusLabel = $claim->status instanceof \App\Enums\ClaimStatusEnum 
                                            ? $claim->status->getLabel() 
                                            : ucfirst((string)$claim->status);
                                    @endphp
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold border {{ $claimStatusClass }}">
                                        {{ $claimStatusLabel }}
                                    </span>
                                </td>
                                <td class="px-6 py-3 text-right whitespace-nowrap">
                                    <a href="{{ route('claims.view', $claim) }}" wire:navigate class="p-1 rounded text-slate-400 hover:text-rose-600 dark:hover:text-rose-400 transition">
                                        <x-heroicon-m-eye class="w-4 h-4 inline" />
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-6 text-center text-xs text-slate-400">
                                    Nenhum sinistro registrado recentemente.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-card>

    </div>

</div>