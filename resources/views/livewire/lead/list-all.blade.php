<x-slot:header>
    <x-hero
        badge="CRM Comercial"
        title="Clientes em Potencial"
        description="Clientes que demonstraram interesse em nossos produtos."
        icon="heroicon-o-user-plus"
    />
</x-slot:header>

<div class="mt-6">
    {{ $this->table }}
</div>