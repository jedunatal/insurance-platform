@extends('layouts.app')

@section('content')

@php
    // Usuário Autenticado
    $authenticated_user = auth()->user() ?? (object)['name' => 'Jorge Eduardo', 'role' => 'Consultor Senior'];

    // Dados focados no ecossistema de seguros e vendas
    $recent_leads = collect([
        (object)['client' => 'Carlos Henrique Ramos', 'product' => 'Auto Comissária', 'status' => 'Novo Lead', 'time' => 'Há 12 min'],
        (object)['client' => 'Mariana Dias Ribeiro', 'product' => 'Vida Individual', 'status' => 'Cotação Pronta', 'time' => 'Há 1 hora']
    ]);

    $critical_renewals = collect([
        (object)['client' => 'Auto Mecânica Silva Ltda', 'product' => 'Empresarial', 'days_left' => 4, 'insurer' => 'Porto Seguro'],
        (object)['client' => 'Roberto de Almeida', 'product' => 'Residencial', 'days_left' => 9, 'insurer' => 'SulAmérica']
    ]);
@endphp

<div class="flex flex-col gap-y-6 w-full">
    
    {{-- Topo: Saudação Executiva Minimalista --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-gray-200 dark:border-neutral-800 pb-5">
        <div>
            <span class="text-xs font-semibold uppercase tracking-wider text-insurance-secondary">Visão Geral</span>
            <h1 class="text-2xl font-bold tracking-tight text-gray-950 dark:text-white mt-0.5">
                Painel Comercial, <span class="text-insurance-primary dark:text-blue-400">{{ $authenticated_user->name }}</span>
            </h1>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('dashboard') }}" class="inline-flex items-center justify-center px-4 py-2 text-sm bg-insurance-primary hover:bg-opacity-90 text-white font-semibold rounded-lg transition-all shadow-xs">
                Ir para o Dashboard
            </a>
        </div>
    </div>

    {{-- 1. Linha de Indicadores Rápidos (Foco total em Corretagem, nada de RH) --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Card 1 -->
        <div class="bg-white dark:bg-neutral-950 border border-gray-200 dark:border-neutral-800 p-4 rounded-xl shadow-xs">
            <p class="text-xs font-medium text-gray-500 dark:text-neutral-400">Minha Produção (Mês)</p>
            <p class="text-xl font-bold text-gray-950 dark:text-white mt-1">R$ 48.250,00</p>
            <span class="text-[10px] text-green-600 dark:text-green-400 font-medium">↑ 12% em relação a maio</span>
        </div>
        <!-- Card 2 -->
        <div class="bg-white dark:bg-neutral-950 border border-gray-200 dark:border-neutral-800 p-4 rounded-xl shadow-xs">
            <p class="text-xs font-medium text-gray-500 dark:text-neutral-400">Leads Ativos</p>
            <p class="text-xl font-bold text-gray-950 dark:text-white mt-1">18 Potenciais</p>
            <span class="text-[10px] text-gray-400 dark:text-neutral-500">Aguardando interação</span>
        </div>
        <!-- Card 3 -->
        <div class="bg-white dark:bg-neutral-950 border border-gray-200 dark:border-neutral-800 p-4 rounded-xl shadow-xs">
            <p class="text-xs font-medium text-gray-500 dark:text-neutral-400">Renovações Críticas</p>
            <p class="text-xl font-bold text-red-600 dark:text-red-400 mt-1">04 Próximos Dias</p>
            <span class="text-[10px] text-red-500/80 font-medium">Risco de quebra de vigência</span>
        </div>
        <!-- Card 4 -->
        <div class="bg-white dark:bg-neutral-950 border border-gray-200 dark:border-neutral-800 p-4 rounded-xl shadow-xs">
            <p class="text-xs font-medium text-gray-500 dark:text-neutral-400">Conversão de Propostas</p>
            <p class="text-xl font-bold text-gray-950 dark:text-white mt-1">68.4%</p>
            <span class="text-[10px] text-insurance-secondary font-medium">Meta interna: 70%</span>
        </div>
    </div>

    {{-- 2. Layout Assimétrico de Acompanhamento Tático --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">
        
        {{-- Bloco Maior (2 colunas no desktop): Últimos Leads Entrados --}}
        <div class="lg:col-span-2 bg-white dark:bg-neutral-950 border border-gray-200 dark:border-neutral-800 rounded-xl shadow-xs overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-200 dark:border-neutral-800 flex items-center justify-between bg-gray-50/50 dark:bg-neutral-900/20">
                <h3 class="text-sm font-bold text-gray-900 dark:text-white">Distribuição Recente de Leads</h3>
                <span class="text-xs text-insurance-primary dark:text-blue-400 font-medium cursor-pointer hover:underline">Ver funil completo →</span>
            </div>
            
            <div class="divide-y divide-gray-100 dark:divide-neutral-800">
                @foreach($recent_leads as $lead)
                    <div class="p-5 flex items-center justify-between hover:bg-gray-50/50 dark:hover:bg-neutral-900/30 transition-colors">
                        <div class="flex flex-col gap-1">
                            <span class="text-sm font-semibold text-gray-900 dark:text-white">{{ $lead->client }}</span>
                            <span class="text-xs text-gray-500 dark:text-neutral-400">Produto solicitado: **{{ $lead->product }}**</span>
                        </div>
                        <div class="flex flex-col items-end gap-1.5">
                            <span class="text-[11px] font-bold px-2.5 py-0.5 rounded-full {{ $lead->status === 'Novo Lead' ? 'bg-blue-50 dark:bg-blue-950/40 text-blue-700 dark:text-blue-400' : 'bg-orange-50 dark:bg-orange-950/40 text-orange-700 dark:text-orange-400' }}">
                                {{ $lead->status }}
                            </span>
                            <span class="text-[10px] text-gray-400 dark:text-neutral-500">{{ $lead->time }}</span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Bloco Menor (1 coluna no desktop): Alertas de Renovações de Apólices --}}
        <div class="bg-white dark:bg-neutral-950 border border-gray-200 dark:border-neutral-800 rounded-xl shadow-xs p-5">
            <div class="flex items-center justify-between pb-3 mb-4 border-b border-gray-100 dark:border-neutral-800">
                <h3 class="text-sm font-bold text-gray-900 dark:text-white">Gatilhos de Renovação</h3>
                <span class="text-[11px] bg-red-50 dark:bg-red-950/30 text-red-700 dark:text-red-400 font-bold px-2 py-0.5 rounded">Urgente</span>
            </div>
            
            <div class="space-y-4">
                @foreach($critical_renewals as $renewal)
                    <div class="p-3 bg-neutral-50 dark:bg-neutral-900 rounded-lg border border-gray-100 dark:border-neutral-800/80 flex flex-col gap-2">
                        <div class="flex justify-between items-start">
                            <span class="text-xs font-bold text-gray-900 dark:text-white truncate max-w-[160px]">{{ $renewal->client }}</span>
                            <span class="text-[10px] font-extrabold text-red-600 dark:text-red-400 bg-red-50 dark:bg-red-950/50 px-1.5 py-0.5 rounded">
                                Vence em {{ $renewal->days_left }} dias
                            </span>
                        </div>
                        <div class="flex justify-between text-[11px] text-gray-500 dark:text-neutral-400">
                            <span>Ramo: {{ $renewal->product }}</span>
                            <span class="font-medium text-gray-700 dark:text-neutral-300">{{ $renewal->insurer }}</span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

    </div>

</div>

@endsection