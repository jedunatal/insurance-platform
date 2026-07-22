<div class="flex flex-col gap-y-6 w-full max-w-7xl mx-auto px-4 sm:px-6 py-2">

    {{-- Cabeçalho da Página no padrão da sua aplicação --}}
    <x-page-header 
        category="CRM Comercial" 
        title="Novo Cliente em Potencial" 
        description="Cadastre as informações para iniciar o acompanhamento no seu funil de vendas."
    >
        <x-slot:actions>
            <a href="{{ route('leads.index') }}" wire:navigate class="inline-flex items-center justify-center px-4 py-2.5 text-sm font-bold text-gray-600 dark:text-neutral-300 bg-gray-100 dark:bg-neutral-900 hover:bg-gray-200 dark:hover:bg-neutral-800 rounded-lg transition-colors">
                ← Voltar para Lista
            </a>
        </x-slot:actions>
    </x-page-header>

    {{-- Card customizado envolvendo o formulário dinâmico do BaseForm --}}
    <x-card class="!p-6">
        <form wire:submit="save">
            {{ $this->form }}
        </form>

        <x-filament-actions::modals />
    </x-card>

</div>