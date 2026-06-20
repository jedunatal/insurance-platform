@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50 py-8 px-4 sm:px-6 lg:px-8">
    <div class="max-w-7xl mx-auto">
        
        <div class="mb-8 border-b border-gray-200 pb-5">
            <h1 class="text-3xl font-bold leading-6 text-gray-900" style="color: #295384;">
                Dashboard Operacional
            </h1>
            <p class="mt-2 text-sm text-gray-500">Visão geral e indicadores da plataforma de seguros.</p>
        </div>

        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4 mb-8">
            <div class="bg-white overflow-hidden shadow rounded-lg border-l-4" style="border-color: #295384;">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="ml-1 w-0 flex-1">
                            <dl>
                               <dt class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Clientes Ativos</dt>
                               <dd class="text-2xl font-bold text-gray-900 mt-1">1,248</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow rounded-lg border-l-4" style="border-color: #B99B6C;">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="ml-1 w-0 flex-1">
                            <dl>
                               <dt class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Cotações em Andamento</dt>
                               <dd class="text-2xl font-bold text-gray-900 mt-1">84</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow rounded-lg border-l-4" style="border-color: #295384;">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="ml-1 w-0 flex-1">
                            <dl>
                               <dt class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Apólices Ativas</dt>
                               <dd class="text-2xl font-bold text-gray-900 mt-1">3,120</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow rounded-lg border-l-4" style="border-color: #B99B6C;">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="ml-1 w-0 flex-1">
                            <dl>
                               <dt class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Sinistros Abertos</dt>
                               <dd class="text-2xl font-bold text-gray-900 mt-1">12</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-8 lg:grid-cols-2">
            
            <div class="bg-white shadow rounded-lg p-6">
                <div class="border-b border-gray-100 pb-4 mb-4">
                    <h2 class="text-lg font-bold" style="color: #295384;">Próximas Renovações</h2>
                </div>
                <div class="flow-root">
                    <ul class="-my-5 divide-y divide-gray-200">
                        <li class="py-4">
                            <div class="flex items-center space-x-4">
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-semibold text-gray-900 truncate">Carlos Henrique Silva</p>
                                    <p class="text-xs text-gray-500 truncate">Seguro Automóvel • Porto Seguro</p>
                                </div>
                                <div>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-50" style="color: #B99B6C;">
                                        Em 5 dias
                                    </span>
                                </div>
                            </div>
                        </li>
                        <li class="py-4">
                            <div class="flex items-center space-x-4">
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-semibold text-gray-900 truncate">M&A Transportes Ltda</p>
                                    <p class="text-xs text-gray-500 truncate">Seguro Empresarial • Allianz</p>
                                </div>
                                <div>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-50" style="color: #B99B6C;">
                                        Em 12 dias
                                    </span>
                                </div>
                            </div>
                        </li>
                        <li class="py-4">
                            <div class="flex items-center space-x-4">
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-semibold text-gray-900 truncate">Fernanda Oliveira Ramos</p>
                                    <p class="text-xs text-gray-500 truncate">Seguro de Vida • SulAmérica</p>
                                </div>
                                <div>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-50" style="color: #B99B6C;">
                                        Em 18 dias
                                    </span>
                                </div>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="bg-white shadow rounded-lg p-6">
                <div class="border-b border-gray-100 pb-4 mb-4">
                    <h2 class="text-lg font-bold" style="color: #295384;">Atividades Recentes</h2>
                </div>
                <div class="flow-root">
                    <ul class="-my-5 divide-y divide-gray-200">
                        <li class="py-4">
                            <div class="flex items-center space-x-4">
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm text-gray-800">
                                        Nova cotação emitida para <span class="font-semibold text-gray-900">Mariana Costa</span> (Residencial)
                                    </p>
                                    <p class="text-xs text-gray-400 mt-0.5">Há 10 minutos</p>
                                </div>
                            </div>
                        </li>
                        <li class="py-4">
                            <div class="flex items-center space-x-4">
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm text-gray-800">
                                        Sinistro <span class="font-semibold text-gray-900">#2026-094</span> atualizado para "Em Análise"
                                    </p>
                                    <p class="text-xs text-gray-400 mt-0.5">Há 1 hora</p>
                                </div>
                            </div>
                        </li>
                        <li class="py-4">
                            <div class="flex items-center space-x-4">
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm text-gray-800">
                                        Apólice de <span class="font-semibold text-gray-900">Roberto Alencar</span> emitida com sucesso
                                    </p>
                                    <p class="text-xs text-gray-400 mt-0.5">Há 3 horas</p>
                                </div>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>

        </div>

    </div>
</div>
@endsection