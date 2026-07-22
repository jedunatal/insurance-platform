<x-slot:header>
    <x-hero
        title="Novo Cliente em Potencial"
        subtitle="CRM Comercial"
        description="Crie um novo lead para organizar e acompanhar suas oportunidades de negócio"
    />
</x-slot:header>

<div class="card bg-white dark:bg-neutral-950 p-5 border border-gray-200 dark:border-neutral-800 rounded-xl shadow-xs">
    <form wire:submit="save">
        {{ $this->form }}
    </form>

    <x-filament-actions::modals/>
</div>