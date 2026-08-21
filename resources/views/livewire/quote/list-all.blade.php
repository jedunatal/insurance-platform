<div class="space-y-6">
    <x-slot:header>
        <x-hero
            badge="Cálculo & Propostas"
            title="Cotações Multi-Seguradoras"
            description="Estudos comparativos, simulação multi-cálculo e emissão de propostas comerciais."
            icon="heroicon-o-calculator"
        />
    </x-slot:header>

    <x-card class="overflow-hidden">
        {{ $this->table }}
    </x-card>
</div>
