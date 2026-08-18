<x-slot:header>
    <x-hero
        badge="Visão Geral"
        title="Painel de Controle"
        description="Acompanhamento operacional em tempo real da carteira, funil de vendas e sinistros."
        icon="heroicon-o-chart-bar"
    />
</x-slot:header>

<div class="mt-6 space-y-6">
    <!-- Grid de Indicadores Operacionais -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Segurados Ativos -->
        <x-card>
            <div class="flex items-center justify-between">
                <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Segurados Ativos</span>
                <span class="p-2 rounded-xl bg-blue-50 text-[#295384] dark:bg-[#295384]/20 dark:text-blue-300">
                    <x-heroicon-m-user-group class="w-5 h-5" />
                </span>
            </div>
            <div class="mt-3">
                <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($metrics['active_insureds'], 0, ',', '.') }}</p>
                <a href="{{ route('insureds.index') }}" wire:navigate class="mt-2 inline-flex items-center text-xs font-medium text-[#295384] dark:text-blue-400 hover:underline">
                    Ver carteira &rarr;
                </a>
            </div>
        </x-card>

        <!-- Apólices Ativas -->
        <x-card>
            <div class="flex items-center justify-between">
                <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Apólices Vigentes</span>
                <span class="p-2 rounded-xl bg-emerald-50 text-emerald-600 dark:bg-emerald-500/10 dark:text-emerald-400">
                    <x-heroicon-m-document-check class="w-5 h-5" />
                </span>
            </div>
            <div class="mt-3">
                <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($metrics['active_policies'], 0, ',', '.') }}</p>
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Prêmio: R$ {{ number_format($metrics['total_active_premium'], 2, ',', '.') }}</p>
            </div>
        </x-card>

        <!-- Leads em Funil -->
        <x-card>
            <div class="flex items-center justify-between">
                <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Leads em Negociação</span>
                <span class="p-2 rounded-xl bg-amber-50 text-amber-600 dark:bg-amber-500/10 dark:text-amber-400">
                    <x-heroicon-m-funnel class="w-5 h-5" />
                </span>
            </div>
            <div class="mt-3">
                <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($metrics['pipeline_leads'], 0, ',', '.') }}</p>
                <a href="{{ route('leads.index') }}" wire:navigate class="mt-2 inline-flex items-center text-xs font-medium text-[#295384] dark:text-blue-400 hover:underline">
                    Ver oportunidades &rarr;
                </a>
            </div>
        </x-card>

        <!-- Sinistros Abertos -->
        <x-card>
            <div class="flex items-center justify-between">
                <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Sinistros em Aberto</span>
                <span class="p-2 rounded-xl bg-rose-50 text-rose-600 dark:bg-rose-500/10 dark:text-rose-400">
                    <x-heroicon-m-exclamation-triangle class="w-5 h-5" />
                </span>
            </div>
            <div class="mt-3">
                <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($metrics['open_claims'], 0, ',', '.') }}</p>
                <a href="{{ route('claims.index') }}" wire:navigate class="mt-2 inline-flex items-center text-xs font-medium text-rose-600 dark:text-rose-400 hover:underline">
                    Acompanhar &rarr;
                </a>
            </div>
        </x-card>
    </div>

    <!-- Seções de Detalhamento Operacional -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        
        <!-- Renovações Críticas -->
        <x-card title="Próximas Renovações" description="Apólices com vencimento previsto para os próximos 30 dias.">
            <x-slot:headerActions>
                <span class="text-xs px-2.5 py-1 bg-amber-50 dark:bg-amber-500/10 text-amber-600 dark:text-amber-400 rounded-lg font-medium">
                    {{ $criticalRenewals->count() }} contratos
                </span>
            </x-slot:headerActions>

            <div class="divide-y divide-gray-100 dark:divide-gray-800 -mx-6 -my-4">
                @forelse($criticalRenewals as $policy)
                    <div class="px-6 py-3.5 flex items-center justify-between hover:bg-gray-50/50 dark:hover:bg-gray-800/30 transition">
                        <div>
                            <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $policy->insured?->name ?? 'Segurado não informado' }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                Apólice #{{ $policy->policy_number }} • {{ $policy->product?->name ?? 'Geral' }}
                            </p>
                        </div>
                        <div class="text-right">
                            <span class="text-xs font-semibold text-amber-600 dark:text-amber-400">
                                Vence {{ $policy->end_date->format('d/m/Y') }}
                            </span>
                            <p class="text-xs text-gray-500 dark:text-gray-400">R$ {{ number_format($policy->total_premium, 2, ',', '.') }}</p>
                        </div>
                    </div>
                @empty
                    <div class="px-6 py-8 text-center text-sm text-gray-400">
                        Nenhuma apólice com renovação crítica nos próximos 30 dias.
                    </div>
                @endforelse
            </div>

            <x-slot:footer>
                <div class="flex justify-end">
                    <a href="{{ route('policies.index') }}" wire:navigate class="text-xs font-medium text-[#295384] dark:text-blue-400 hover:underline">
                        Ver todas as apólices &rarr;
                    </a>
                </div>
            </x-slot:footer>
        </x-card>

        <!-- Sinistros Recentes -->
        <x-card title="Avisos Recentes de Sinistro" description="Ocorrências registradas aguardando regulação.">
            <div class="divide-y divide-gray-100 dark:divide-gray-800 -mx-6 -my-4">
                @forelse($recentClaims as $claim)
                    <div class="px-6 py-3.5 flex items-center justify-between hover:bg-gray-50/50 dark:hover:bg-gray-800/30 transition">
                        <div>
                            <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $claim->insured?->name }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                Sinistro #{{ $claim->claim_number ?? 'S/N' }} • {{ $claim->occurrence_date->format('d/m/Y H:i') }}
                            </p>
                        </div>
                        <div class="text-right">
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300">
                                {{ $claim->status->getLabel() }}
                            </span>
                        </div>
                    </div>
                @empty
                    <div class="px-6 py-8 text-center text-sm text-gray-400">
                        Nenhum aviso de sinistro registrado recentemente.
                    </div>
                @endforelse
            </div>

            <x-slot:footer>
                <div class="flex justify-end">
                    <a href="{{ route('claims.index') }}" wire:navigate class="text-xs font-medium text-rose-600 dark:text-rose-400 hover:underline">
                        Ver todos os sinistros &rarr;
                    </a>
                </div>
            </x-slot:footer>
        </x-card>

    </div>
</div>