<x-slot:header>
    <x-hero
        badge="Clientes Efetivados"
        title="Segurados"
        description="Gestão da carteira de clientes ativos e cadastros gerais da corretora."
    />
</x-slot:header>

<div class="mt-4">
    {{ $this->table }}
</div>