@extends('layouts.app')

@section('content')
    @php
        $metrics = [
            [
                'label' => 'Clientes Ativos',
                'value' => '1.248',
                'icon' =>
                    '<svg class="h-5 w-5 text-blue-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15 9.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0ZM6.963 18.251a5.002 5.002 0 0 0 10.074 0M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>',
            ],
            [
                'label' => 'Cotações em Andamento',
                'value' => '84',
                'icon' =>
                    '<svg class="h-5 w-5 text-[#B99B6C]" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" /></svg>',
            ],
            [
                'label' => 'Apólices Ativas',
                'value' => '3.120',
                'icon' =>
                    '<svg class="h-5 w-5 text-blue-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M10.125 2.25h-4.5c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125v-9M10.125 2.25h.375a9 9 0 0 1 9 9v.375M10.125 2.25A3.375 3.375 0 0 1 13.5 5.625v1.5c0 .621.504 1.125 1.125 1.125h1.5a3.375 3.375 0 0 1 3.375 3.375M9 15l2.25 2.25L15 12" /></svg>',
            ],
            [
                'label' => 'Sinistros Abertos',
                'value' => '12',
                'icon' =>
                    '<svg class="h-5 w-5 text-rose-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" /></svg>',
            ],
        ];

        $renewals = [
            ['name' => 'Carlos Henrique Silva', 'details' => 'Seguro Automóvel • Porto Seguro', 'time' => 'Em 5 dias'],
            ['name' => 'M&A Transportes Ltda', 'details' => 'Seguro Empresarial • Allianz', 'time' => 'Em 12 dias'],
            ['name' => 'Fernanda Oliveira Ramos', 'details' => 'Seguro de Vida • SulAmérica', 'time' => 'Em 18 dias'],
        ];

        $activities = [
            [
                'text' =>
                    'Nova cotação emitida para <span class="font-bold text-white">Mariana Costa</span> (Residencial)',
                'time' => 'Há 10 minutos',
            ],
            [
                'text' => 'Sinistro <span class="font-bold text-white">#2026-094</span> atualizado para "Em Análise"',
                'time' => 'Há 1 hora',
            ],
            [
                'text' => 'Apólice de <span class="font-bold text-white">Roberto Alencar</span> emitida com sucesso',
                'time' => 'Há 3 horas',
            ],
        ];
    @endphp

    <div class="flex flex-col gap-y-6 w-full max-w-7xl mx-auto">

        <div class="pb-2">
            <h1 class="text-3xl font-extrabold tracking-tight text-white">
                Dashboard Operacional
            </h1>
            <p class="mt-1 text-xs text-slate-400">Visão geral e indicadores da plataforma de seguros em tempo real.</p>
        </div>

        {{-- Grid de Métricas Bento --}}
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
            @foreach ($metrics as $metric)
                <div
                    class="bg-slate-900/60 border border-slate-800/80 p-5 rounded-2xl shadow-xl backdrop-blur-md flex items-center justify-between">
                    <div>
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">{{ $metric['label'] }}</p>
                        <p class="text-2xl font-extrabold text-white mt-1">{{ $metric['value'] }}</p>
                    </div>
                    <div class="p-3 bg-slate-950/60 border border-slate-800 rounded-xl">
                        {!! $metric['icon'] !!}
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Grid Duplo --}}
        <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">

            <x-card title="Próximas Renovações">
                <ul class="divide-y divide-slate-800/60">
                    @foreach ($renewals as $renewal)
                        <li class="py-3.5 first:pt-0 last:pb-0 flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div
                                    class="h-9 w-9 rounded-xl bg-[#295384]/20 border border-[#295384]/40 text-[#B99B6C] flex items-center justify-center font-bold text-xs">
                                    {{ collect(explode(' ', $renewal['name']))->map(fn($n) => $n[0])->take(2)->join('') }}
                                </div>
                                <div>
                                    <p class="text-xs font-bold text-white">{{ $renewal['name'] }}</p>
                                    <p class="text-[11px] text-slate-400 mt-0.5">{{ $renewal['details'] }}</p>
                                </div>
                            </div>
                            <span
                                class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-amber-500/10 text-[#B99B6C] border border-amber-500/20">
                                {{ $renewal['time'] }}
                            </span>
                        </li>
                    @endforeach
                </ul>
            </x-card>

            <x-card title="Atividades Recentes">
                <div class="space-y-4">
                    @foreach ($activities as $activity)
                        <div class="flex items-start gap-3">
                            <div class="w-2 h-2 rounded-full bg-[#B99B6C] mt-1.5 shrink-0 animate-pulse"></div>
                            <div>
                                <p class="text-xs text-slate-300 leading-relaxed">{!! $activity['text'] !!}</p>
                                <p class="text-[10px] text-slate-500 mt-0.5">{{ $activity['time'] }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </x-card>

        </div>
    </div>
@endsection
