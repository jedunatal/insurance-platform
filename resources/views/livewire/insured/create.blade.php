<x-slot:header>
    <x-hero
        badge="Novo Cadastro"
        title="Cadastrar Segurado"
        description="Preencha os dados cadastrais e o endereço completo do cliente."
    />
</x-slot:header>

<form wire:submit="create" class="mt-4 space-y-6">
    {{ $this->form }}

    <div class="flex items-center gap-x-3">
        <button type="submit" class="px-5 py-2.5 bg-[#295384] hover:bg-[#1c385a] text-white font-semibold rounded-lg text-sm transition-colors shadow-xs">
            Salvar Segurado
        </button>
        <a href="{{ route('insureds.index') }}" wire:navigate class="px-5 py-2.5 bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-200 font-semibold rounded-lg text-sm hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors">
            Cancelar
        </a>
    </div>
</form>