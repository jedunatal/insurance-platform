<x-slot:header>
    <x-hero
        title="Leads"
        subtitle="Módulo de CRM"
        description="Gerencie, filtre e acompanhe o funil de leads do sistema"
    />
</x-slot:header>

<div class="card bg-white p-5 space-y-4">
    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <div class="flex flex-1 flex-col gap-3 sm:flex-row sm:items-center">
            <div class="flex-1">
                <input 
                    wire:model.live.debounce.300ms="search" 
                    type="text" 
                    placeholder="Buscar por nome, e-mail..." 
                    class="form-control w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                />
            </div>

            <div class="w-full sm:w-48">
                <select 
                    wire:model.live="statusFilter" 
                    class="form-select w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                >
                    <option value="">Todos os Status</option>
                    @foreach($this->statuses as $status)
                        <option value="{{ $status->value }}">{{ $status->name }}</option>
                    @endforeach
                </select>
            </div>

            @if(filled($search) || filled($statusFilter))
                <div>
                    <button 
                        wire:click="clearFilters" 
                        type="button" 
                        class="btn btn-secondary px-3 py-2 text-sm text-gray-600 bg-gray-100 rounded-lg hover:bg-gray-200"
                    >
                        Limpar
                    </button>
                </div>
            @endif
        </div>

        <div class="flex justify-end">
            <a href="{{ route('leads.create') }}" class="btn btn-primary px-4 py-2 text-sm font-semibold text-white bg-blue-600 rounded-lg hover:bg-blue-500">
                Novo Lead
            </a>
        </div>
    </div>

    <div class="overflow-x-auto border border-gray-100 rounded-lg">
        <table class="table w-full text-sm text-left text-gray-500">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50/70 border-b border-gray-100">
                <tr>
                    <th class="px-6 py-3 font-semibold">Nome</th>
                    <th class="px-6 py-3 font-semibold">E-mail</th>
                    <th class="px-6 py-3 font-semibold">Status</th>
                    <th class="px-6 py-3 font-semibold text-right">Ações</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($this->leads as $lead)
                    <tr class="hover:bg-gray-50/50 transition-colors">
                        <td class="px-6 py-4 font-medium text-gray-950">{{ $lead->name }}</td>
                        <td class="px-6 py-4 text-gray-600">{{ $lead->email }}</td>
                        <td class="px-6 py-4">
                            <span class="badge inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                {{ $lead->status?->name ?? 'Sem Status' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right space-x-3">
                            <a href="{{ route('leads.edit', $lead) }}" class="text-blue-600 hover:text-blue-900 font-medium">
                                Editar
                            </a>
                            <button 
                                wire:click="confirmDelete({{ $lead->id }})" 
                                type="button" 
                                class="text-red-600 hover:text-red-900 font-medium"
                            >
                                Excluir
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-6 py-12 text-center text-gray-400">
                            Nenhum lead encontrado para os critérios informados.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="pt-2">
        {{ $this->leads->links() }}
    </div>

    <x-filament-actions::modals />
</div>