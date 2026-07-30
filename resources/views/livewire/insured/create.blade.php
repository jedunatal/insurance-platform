<div class="flex flex-col gap-y-6 w-full max-w-5xl mx-auto px-4 sm:px-6 py-2">

    {{-- Cabeçalho da Página --}}
    <x-page-header 
        category="Segurados" 
        title="Novo segurado" 
        description="Cadastre um novo segurado."
    >
        <x-slot:actions>
            <a href="{{ route('insureds.index') }}" wire:navigate class="inline-flex items-center gap-x-2 text-sm font-semibold text-gray-600 dark:text-neutral-400 hover:text-gray-950 dark:hover:text-white transition-colors">
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
                    :cancel-url="route('insureds.index')" 
                    submit-text="Cadastrar Segurado" 
                />
            </x-slot:footer>
        </x-card>
    </form>

    <x-filament-actions::modals />
</div>