<x-slot:header>
    <div class="rounded-xl bg-white dark:bg-[#1F2937] border border-gray-200 dark:border-gray-700/70 p-6 shadow-xs transition-colors">
        <x-hero
            title="Clientes em Potencial"
            subtitle="CRM Comercial"
            description="Gerencie, filtre e acompanhe o seu funil de negociações."
        />
    </div>
</x-slot:header>

<div class="mt-4">
    {{ $this->table }}
</div>