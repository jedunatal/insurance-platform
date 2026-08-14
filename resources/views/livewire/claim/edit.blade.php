<div class="flex flex-col gap-y-6 w-full max-w-5xl mx-auto px-4 sm:px-6 py-2">

    {{-- Cabeçalho Padronizado --}}
    <x-page-header
        category="Edição de Sinistro"
        :title="$record->claim_number"
        description="Atualize o andamento do processo de sinistro."
    >
        <x-slot:actions>
            <a href="{{ route('claims.view', $record) }}" wire:navigate class="inline-flex items-center px-3.5 py-2 text-sm font-semibold text-gray-700 dark:text-neutral-300 bg-gray-100 dark:bg-neutral-900 rounded-lg hover:bg-gray-200 dark:hover:bg-neutral-800 transition-colors">
                Visualizar
            </a>
            <a href="{{ route('claims.index') }}" wire:navigate class="inline-flex items-center gap-x-2 text-sm font-semibold text-gray-600 dark:text-neutral-400 hover:text-gray-950 dark:hover:text-white transition-colors">
                ← Voltar
            </a>
        </x-slot:actions>
    </x-page-header>

    {{-- Formulário envelopado pelo Card e FormActions reutilizáveis --}}
    <form wire:submit="save">
        <x-card>
            {{ $this->form }}

            <x-slot:footer>
                <x-form-actions
                    :cancel-url="route('claims.index')"
                    submit-text="Atualizar Sinistro"
                />
            </x-slot:footer>
        </x-card>
    </form>

    <x-filament-actions::modals />
</div>
