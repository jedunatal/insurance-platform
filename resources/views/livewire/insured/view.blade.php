<x-slot:header>
    <x-hero
        badge="Ficha do Cliente"
        title="{{ $record->name }}"
        description="Visualização dos dados gerais e endereço do segurado."
    />
</x-slot:header>

<div class="mt-4 space-y-6">
    {{ $this->insuredSchema }}

    <div>
        <a href="{{ route('insureds.index') }}" wire:navigate class="px-5 py-2.5 bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-200 font-semibold rounded-lg text-sm hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors">
            Voltar para Listagem
        </a>
    </div>
</div>