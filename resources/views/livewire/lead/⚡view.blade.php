<x-slot:header>
    <x-hero
        title="Detalhes do Lead"
        subtitle="Leads"
        description="Visualize as informações consolidadas deste registro"
    />
</x-slot:header>

<div class="card bg-white p-5 space-y-6">
    @if(method_exists($this, 'infolist'))
        {{ $this->infolist }}
    @else
        <div class="pointer-events-none opacity-80">
            {{ $this->form }}
        </div>
    @endif

    <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100">
        <a href="{{ route('leads.index') }}" class="btn btn-secondary px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200">
            Voltar para Listagem
        </a>
        <a href="{{ route('leads.edit', $record) }}" class="btn btn-warning px-4 py-2 text-sm font-semibold text-white bg-amber-600 rounded-lg hover:bg-amber-500">
            Editar Dados
        </a>
    </div>

    <x-filament-actions::modals />
</div>