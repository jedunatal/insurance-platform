<x-slot:header>
    <x-hero
        badge="Apólices Efetivadas"
        title="Apólices"
        description="Gestão da carteira de apólices ativas e cadastros gerais da corretora."
        icon="heroicon-o-document-text"
    />
</x-slot:header>
<div class="mt-4">
    {{ $this->table }}
</div>