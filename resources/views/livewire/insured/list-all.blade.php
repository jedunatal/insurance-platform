<x-slot:header>
    <x-hero
        badge="Carteira de Clientes"
        title="Segurados"
        description="Gestão de clientes efetivados (Pessoas Físicas e Jurídicas)."
        icon="heroicon-o-users"
    />
</x-slot:header>

<div class="mt-4 space-y-4">
    <div class="flex items-center justify-start">
        <a href="{{ route('dashboard') }}" wire:navigate class="inline-flex items-center gap-2 px-3.5 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-200 rounded-xl shadow-xs hover:bg-gray-50 hover:text-[#295384] dark:bg-gray-800 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-700 dark:hover:text-white transition">
            <x-heroicon-m-home class="w-4 h-4 text-gray-400" />
            <span>Início</span>
        </a>
    </div>

    {{ $this->table }}
</div>