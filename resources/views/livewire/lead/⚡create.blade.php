<x-slot:header>
    <x-hero
        title="Novo Lead"
        subtitle="Leads"
        description="Cadastre um novo lead na plataforma para iniciar o acompanhamento"
    />
</x-slot:header>

<div class="card bg-white p-5">
    <form wire:submit="save" class="space-y-6">
        {{ $this->form }}

        <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100">
            <a href="{{ route('leads.index') }}" class="btn btn-secondary px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200">
                Cancelar
            </a>
            <button type="submit" class="btn btn-primary px-4 py-2 text-sm font-semibold text-white bg-blue-600 rounded-lg hover:bg-blue-500">
                Salvar Lead
            </button>
        </div>
    </form>

    <x-filament-actions::modals />
</div>