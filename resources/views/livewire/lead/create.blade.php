<div class="flex flex-col gap-y-6 w-full max-w-5xl mx-auto px-4 sm:px-6 py-2">

    {{-- Cabeçalho da Página --}}
    <x-page-header 
        category="CRM Comercial" 
        title="Novo Cliente em Potencial" 
        description="Cadastre as informações para iniciar o acompanhamento no seu funil de vendas."
    >
        <x-slot:actions>
            <a href="{{ route('leads.index') }}" wire:navigate class="inline-flex items-center gap-x-2 text-sm font-semibold text-gray-600 dark:text-neutral-400 hover:text-gray-950 dark:hover:text-white transition-colors">
                ← Voltar para Lista
            </a>
        </x-slot:actions>
    </x-page-header>

    {{-- Formulário com Card e Rodapé de Ações --}}
    <form wire:submit="save">
        <x-card class="p-6 dark:!bg-[#1F2937] dark:!border-gray-700">
            {{ $this->form }}

            <x-slot:footer>
                <x-form-actions 
                    :cancel-url="route('leads.index')" 
                    submit-text="Cadastrar Cliente" 
                />
            </x-slot:footer>
        </x-card>
    </form>

    <x-filament-actions::modals />
</div>