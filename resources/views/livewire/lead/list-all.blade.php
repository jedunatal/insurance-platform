<div class="flex flex-col gap-y-6 w-full max-w-7xl mx-auto px-4 sm:px-6 py-2">

    {{-- Cabeçalho da Listagem --}}
    <x-page-header 
        category="CRM Comercial" 
        title="Clientes em Potencial" 
        description="Gerencie, filtre e acompanhe o seu funil de negociações."
    >
        <x-slot:actions>
            <a href="{{ route('leads.create') }}" wire:navigate class="inline-flex items-center justify-center px-4 py-2.5 text-sm font-bold text-white bg-[#295384] hover:bg-opacity-90 rounded-lg transition-colors shadow-xs">
                + Novo Potencial
            </a>
        </x-slot:actions>
    </x-page-header>

    {{-- Card de Filtros Reativo --}}
    <x-card class="!p-5 dark:!bg-[#1F2937] dark:!border-gray-700">
        <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
            <div class="flex flex-1 flex-col gap-3 sm:flex-row sm:items-center">
                
                {{-- Input de Pesquisa --}}
                <div class="flex-1">
                    <input 
                        wire:model.live.debounce.300ms="search" 
                        type="text" 
                        placeholder="Buscar por nome, e-mail ou telefone..." 
                        class="w-full rounded-lg border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-[#111827] text-gray-900 dark:text-white placeholder:text-gray-400 dark:placeholder:text-gray-400 focus:border-[#295384] focus:ring-[#295384] text-sm py-2 px-3 shadow-xs"
                    />
                </div>

                {{-- Select de Status --}}
                <div class="w-full sm:w-52">
                    <select 
                        wire:model.live="statusFilter" 
                        class="w-full rounded-lg border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-[#111827] text-gray-900 dark:text-white focus:border-[#295384] focus:ring-[#295384] text-sm py-2 px-3 shadow-xs"
                    >
                        <option value="">Todos os Status</option>
                        @foreach($this->statuses as $status)
                            <option value="{{ $status->value }}">{{ $status->name }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Botão para Resetar Filtros --}}
                @if(filled($search) || filled($statusFilter))
                    <div>
                        <button 
                            wire:click="clearFilters" 
                            type="button" 
                            class="px-3.5 py-2 text-sm font-semibold text-gray-600 dark:text-gray-200 bg-gray-100 dark:bg-[#111827] hover:bg-gray-200 dark:hover:bg-gray-700 rounded-lg transition-colors"
                        >
                            Limpar
                        </button>
                    </div>
                @endif
            </div>
        </div>
    </x-card>

    {{-- Tabela de Dados --}}
    <div class="overflow-x-auto border border-gray-200 dark:border-gray-700 rounded-xl bg-white dark:bg-[#1F2937] shadow-xs">
        <table class="w-full text-sm text-left text-gray-500 dark:text-gray-300">
            <thead class="text-xs text-gray-700 dark:text-gray-300 uppercase bg-gray-50/80 dark:bg-[#111827]/50 border-b border-gray-200 dark:border-gray-700 font-bold">
                <tr>
                    <th class="px-6 py-3.5">Cliente / Ramo</th>
                    <th class="px-6 py-3.5">Contato</th>
                    <th class="px-6 py-3.5">Status</th>
                    <th class="px-6 py-3.5 text-right">Ações</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                @forelse($this->leads as $lead)
                    <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-800/40 transition-colors">
                        <td class="px-6 py-4">
                            <a href="{{ route('leads.show', $lead) }}" wire:navigate class="font-bold text-gray-950 dark:text-white hover:text-[#295384] dark:hover:text-blue-400 transition-colors">
                                {{ $lead->name }}
                            </a>
                            <p class="text-xs text-[#B99B6C] font-semibold mt-0.5">
                                {{ $lead->product?->name ?? 'Ramo não informado' }}
                            </p>
                        </td>

                        <td class="px-6 py-4">
                            <p class="text-gray-600 dark:text-gray-300">{{ $lead->email ?? 'Sem e-mail' }}</p>
                            @if($lead->phone)
                                <p class="text-xs text-emerald-600 dark:text-emerald-400 font-semibold mt-0.5">
                                    {{ $lead->phone }}
                                </p>
                            @endif
                        </td>

                        <td class="px-6 py-4">
                            <x-status-badge 
                                :status="$lead->status?->name ?? 'Novo Potencial'" 
                                color="blue" 
                            />
                        </td>

                        <td class="px-6 py-4 text-right space-x-3 font-semibold">
                            <a href="{{ route('leads.show', $lead) }}" wire:navigate class="text-gray-500 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white transition-colors">
                                Ver
                            </a>
                            <a href="{{ route('leads.edit', $lead) }}" wire:navigate class="text-[#295384] dark:text-blue-400 hover:underline">
                                Editar
                            </a>
                            <button 
                                wire:click="confirmDelete({{ $lead->id }})" 
                                type="button" 
                                class="text-red-600 dark:text-red-400 hover:underline"
                            >
                                Excluir
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="p-0">
                            {{-- Empty State Reutilizável --}}
                            <x-empty-state 
                                title="Nenhum cliente em potencial encontrado" 
                                description="Não encontramos registros correspondentes à pesquisa ou filtro selecionado." 
                            />
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Paginação --}}
    <div class="pt-2">
        {{ $this->leads->links() }}
    </div>

    <x-filament-actions::modals />
</div>