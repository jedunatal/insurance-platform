@extends('layouts.app')

@section('content')

@php
    // Estrutura de dados organizada para manter o código limpo (DRY)
    $metrics = [
        [
            'label' => 'Clientes Ativos',
            'value' => '1.248',
            'border_color' => 'border-l-[#295384]',
            'icon' => '<svg class="h-5 w-5 text-[#295384] dark:text-blue-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15 9.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0ZM6.963 18.251a5.002 5.002 0 0 0 10.074 0M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>'
        ],
        [
            'label' => 'Cotações em Andamento',
            'value' => '84',
            'border_color' => 'border-l-[#B99B6C]',
            'icon' => '<svg class="h-5 w-5 text-[#B99B6C] dark:text-amber-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" /></svg>'
        ],
        [
            'label' => 'Apólices Ativas',
            'value' => '3.120',
            'border_color' => 'border-l-[#295384]',
            'icon' => '<svg class="h-5 w-5 text-[#295384] dark:text-blue-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M10.125 2.25h-4.5c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125v-9M10.125 2.25h.375a9 9 0 0 1 9 9v.375M10.125 2.25A3.375 3.375 0 0 1 13.5 5.625v1.5c0 .621.504 1.125 1.125 1.125h1.5a3.375 3.375 0 0 1 3.375 3.375M9 15l2.25 2.25L15 12" /></svg>'
        ],
        [
            'label' => 'Sinistros Abertos',
            'value' => '12',
            'border_color' => 'border-l-red-500 dark:border-l-red-600',
            'icon' => '<svg class="h-5 w-5 text-red-500 dark:text-red-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" /></svg>'
        ]
    ];

    $renewals = [
        ['name' => 'Carlos Henrique Silva', 'details' => 'Seguro Automóvel • Porto Seguro', 'time' => 'Em 5 dias'],
        ['name' => 'M&A Transportes Ltda', 'details' => 'Seguro Empresarial • Allianz', 'time' => 'Em 12 dias'],
        ['name' => 'Fernanda Oliveira Ramos', 'details' => 'Seguro de Vida • SulAmérica', 'time' => 'Em 18 dias'],
    ];

    $activities = [
        ['text' => 'Nova cotação emitida para <span class="font-bold text-gray-950 dark:text-white">Mariana Costa</span> (Residencial)', 'time' => 'Há 10 minutos'],
        ['text' => 'Sinistro <span class="font-bold text-gray-950 dark:text-white">#2026-094</span> atualizado para "Em Análise"', 'time' => 'Há 1 hora'],
        ['text' => 'Apólice de <span class="font-bold text-gray-950 dark:text-white">Roberto Alencar</span> emitida com sucesso', 'time' => 'Há 3 horas'],
    ];
@endphp

<div class="flex flex-col gap-y-8 w-full max-w-7xl mx-auto">
    
    {{-- Topo do Dashboard --}}
    <div class="border-b border-gray-200 dark:border-neutral-800 pb-5">
        <h1 class="text-3xl font-extrabold tracking-tight text-[#295384] dark:text-blue-400">
            Dashboard Operacional
        </h1>
        <p class="mt-2 text-sm text-gray-500 dark:text-neutral-400">Visão geral e indicadores da plataforma de seguros.</p>
    </div>

    {{-- Grid de Cards Reativos ao Dark Mode --}}
    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
        @foreach($metrics as $metric)
            <div class="bg-white dark:bg-neutral-950 overflow-hidden border border-gray-200 dark:border-neutral-800 shadow-xs rounded-xl border-l-4 {{ $metric['border_color'] }} p-5 transition-colors">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <p class="text-xs font-bold text-gray-400 dark:text-neutral-500 uppercase tracking-wider">{{ $metric['label'] }}</p>
                        <p class="text-3xl font-extrabold text-gray-950 dark:text-white mt-1.5">{{ $metric['value'] }}</p>
                    </div>
                    <div class="p-2 bg-gray-50 dark:bg-neutral-900 rounded-lg">
                        {!! $metric['icon'] !!}
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    {{-- Grid Duplo: Renovações e Atividades Recentes --}}
    <div class="grid grid-cols-1 gap-8 lg:grid-cols-2">
        
        {{-- Card: Próximas Renovações --}}
        <div class="bg-white dark:bg-neutral-950 border border-gray-200 dark:border-neutral-800 rounded-xl p-6 shadow-xs transition-colors">
            <div class="border-b border-gray-100 dark:border-neutral-900 pb-4 mb-4">
                <h2 class="text-lg font-bold text-[#295384] dark:text-blue-400">Próximas Renovações</h2>
            </div>
            
            <div class="flow-root">
                <ul class="divide-y divide-gray-100 dark:divide-neutral-900">
                    @foreach($renewals as $renewal)
                        <li class="py-4.5 first:pt-0 last:pb-0">
                            <div class="flex items-center justify-between gap-4">
                                <div class="flex items-center gap-3">
                                    <!-- Pequeno placeholder visual para humanizar a lista de clientes -->
                                    <div class="h-8 w-8 rounded-full bg-[#295384]/10 text-[#295384] dark:text-blue-400 flex items-center justify-center font-bold text-xs">
                                        {{ collect(explode(' ', $renewal['name']))->map(fn($n) => $n[0])->take(2)->join('') }}
                                    </div>
                                    <div>
                                        <p class="text-sm font-bold text-gray-950 dark:text-white">{{ $renewal['name'] }}</p>
                                        <p class="text-xs text-gray-500 dark:text-neutral-400 mt-0.5">{{ $renewal['details'] }}</p>
                                    </div>
                                </div>
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-50 dark:bg-amber-950/30 text-[#B99B6C] dark:text-amber-400 border border-[#B99B6C]/20">
                                    {{ $renewal['time'] }}
                                </span>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>

        {{-- Card: Atividades Recentes (Estilizado em formato de Timeline) --}}
        <div class="bg-white dark:bg-neutral-950 border border-gray-200 dark:border-neutral-800 rounded-xl p-6 shadow-xs transition-colors">
            <div class="border-b border-gray-100 dark:border-neutral-900 pb-4 mb-6">
                <h2 class="text-lg font-bold text-[#295384] dark:text-blue-400">Atividades Recentes</h2>
            </div>
            
            <div class="flow-root">
                <ul role="list" class="-mb-8">
                    @foreach($activities as $index => $activity)
                        <li>
                            <div class="relative pb-8">
                                {{-- Linha vertical de conexão entre as bolinhas da timeline --}}
                                @if($index < count($activities) - 1)
                                    <span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200 dark:bg-neutral-800" aria-hidden="true"></span>
                                @endif
                                
                                <div class="relative flex space-x-3">
                                    <div>
                                        <span class="h-8 w-8 rounded-full bg-gray-50 dark:bg-neutral-900 flex items-center justify-center ring-8 ring-white dark:ring-neutral-950">
                                            <div class="h-2.5 w-2.5 rounded-full bg-[#B99B6C] dark:bg-amber-500"></div>
                                        </span>
                                    </div>
                                    <div class="flex-1 min-w-0 pt-1.5">
                                        <p class="text-sm text-gray-700 dark:text-neutral-300">
                                            {!! $activity['text'] !!}
                                        </p>
                                        <p class="text-xs text-gray-400 dark:text-neutral-500 mt-1">{{ $activity['time'] }}</p>
                                    </div>
                                </div>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>

    </div>

</div>

@endsection