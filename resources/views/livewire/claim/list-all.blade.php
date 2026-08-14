<x-slot:header>
    <x-hero
        badge="Gestão de Sinistros"
        title="Avisos de Sinistro"
        description="Acompanhamento e registro de sinistros da corretora."
        icon="heroicon-o-shield-exclamation"
    />
</x-slot:header>

<div class="mt-4">
    {{ $this->table }}
</div>
