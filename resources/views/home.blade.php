@extends('layouts.app')

@section('content')

@php
    $authenticated_user = auth()->user() ?? (object)['name' => 'Jorge Eduardo', 'role' => 'Consultor Senior'];

    $recent_leads = collect([
        (object)['client' => 'Carlos Henrique Ramos', 'product' => 'Auto Comissária', 'status' => 'Novo Potencial', 'time' => 'Há 12 min'],
        (object)['client' => 'Mariana Dias Ribeiro', 'product' => 'Vida Individual', 'status' => 'Cotação Pronta', 'time' => 'Há 1 hora']
    ]);

    $critical_renewals = collect([
        (object)['client' => 'Auto Mecânica Silva Ltda', 'product' => 'Empresarial', 'days_left' => 4, 'insurer' => 'Porto Seguro'],
        (object)['client' => 'Roberto de Almeida', 'product' => 'Residencial', 'days_left' => 9, 'insurer' => 'SulAmérica']
    ]);
@endphp

<div class="flex flex-col gap-y-8 w-full max-w-7xl mx-auto px-4 sm:px-6">
    
    {{-- Cabeçalho --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-gray-100 dark:border-neutral-900 pb-6">
        <div>
            <span class="text-xs font-bold uppercase tracking-widest text-[#B99B6C]">Espaço de Trabalho</span>
            <h1 class="text-3xl font-extrabold tracking-tight text-gray-950 dark:text-white mt-1">
                Olá, <span class="text-[#295384] dark:text-blue-400">{{ $authenticated_user->name }}</span>
            </h1>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('dashboard') }}" class="inline-flex items-center justify-center px-4 py-2.5 text-sm font-semibold text-gray-700 dark:text-neutral-300 bg-white dark:bg-neutral-950 border border-gray-200 dark:border-neutral-800 hover:bg-gray-50 dark:hover:bg-neutral-900 rounded-lg transition-all shadow-xs">
                Ver Métricas no Dashboard
            </a>
        </div>
    </div>

    {{-- Atalhos Rápidos --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <a href="{{ route('leads.create') }}" class="group flex flex-col items-start p-5 bg-white dark:bg-neutral-950 border border-gray-200 dark:border-neutral-800 rounded-xl hover:border-[#295384] dark:hover:border-[#295384] hover:shadow-md transition-all">
            <div class="p-3 bg-blue-50 dark:bg-[#295384]/10 text-[#295384] dark:text-blue-400 rounded-lg group-hover:bg-[#295384] group-hover:text-white transition-all">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M18 7.5v3m0 0v3m0-3h3m-3 0h-3m-2.25-4.125a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0ZM3 19.235v-.11a6.375 6.375 0 0 1 12.75 0v.109A12.318 12.318 0 0 1 9.374 21c-2.331 0-4.512-.645-6.374-1.766Z" /></svg>
            </div>
            <span class="text-sm font-bold text-gray-900 dark:text-white mt-4">Clientes em Potencial</span>
        </a>
        
        <!-- Botões desativados temporariamente -->
        @foreach(['Emitir Apólice', 'Avisar Sinistro', 'Novo Segurado'] as $label)
            <button class="flex flex-col items-start p-5 bg-white dark:bg-neutral-950 border border-gray-200 dark:border-neutral-800 rounded-xl opacity-50 cursor-not-allowed">
                <div class="p-3 bg-gray-100 dark:bg-neutral-900 text-gray-400 rounded-lg">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m6-6H6" /></svg>
                </div>
                <span class="text-sm font-bold text-gray-900 dark:text-white mt-4">{{ $label }}</span>
            </button>
        @endforeach
    </div>

    {{-- Grid Principal --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        {{-- Fila de Negociação --}}
        <div class="lg:col-span-2 bg-white dark:bg-neutral-950 border border-gray-200 dark:border-neutral-800 rounded-xl shadow-xs overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-200 dark:border-neutral-800 flex justify-between items-center bg-gray-50/50 dark:bg-neutral-900/10">
                <h3 class="text-base font-bold text-gray-950 dark:text-white">Fila de Negociação</h3>
                <a href="{{ route('leads.index') }}" class="text-xs text-[#295384] dark:text-blue-400 font-bold hover:underline">Ver Funil →</a>
            </div>
            
            <div class="divide-y divide-gray-100 dark:divide-neutral-800">
                @foreach($recent_leads as $lead)
                    <div class="p-6 flex items-center justify-between">
                        <div>
                            <h4 class="text-sm font-bold text-gray-950 dark:text-white">{{ $lead->client }}</h4>
                            <p class="text-xs text-gray-500 dark:text-neutral-400">Ramo: <span class="font-bold text-gray-900 dark:text-neutral-300">{{ $lead->product }}</span></p>
                        </div>
                        <a href="{{ route('leads.index') }}" class="px-3 py-1.5 text-xs font-bold text-white bg-[#295384] rounded-md">Atender</a>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Renovações --}}
        <div class="bg-white dark:bg-neutral-950 border border-gray-200 dark:border-neutral-800 rounded-xl shadow-xs p-6">
            <h3 class="text-base font-bold text-gray-950 dark:text-white mb-5">Renovações Críticas</h3>
            <div class="space-y-4">
                @foreach($critical_renewals as $renewal)
                    <div class="p-4 bg-neutral-50 dark:bg-neutral-900 rounded-lg border border-neutral-200 dark:border-neutral-800">
                        <div class="flex justify-between mb-2">
                            <span class="text-xs font-extrabold text-gray-900 dark:text-white">{{ $renewal->client }}</span>
                            <span class="text-[10px] font-bold text-red-600 bg-red-50 dark:bg-red-950/40 px-2 py-0.5 rounded">Vence em {{ $renewal->days_left }} dias</span>
                        </div>
                        <span class="text-[11px] font-bold text-[#B99B6C]">{{ $renewal->insurer }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

@endsection