<div class="flex flex-col gap-y-6 w-full max-w-5xl mx-auto px-4 sm:px-6 py-2">

    <x-page-header
        category="Ficha de Atendimento"
        :title="$record->name"
        description="Informações consolidadas e acompanhamento de prospecção."
    >
        <x-slot:actions>
            <a
                href="{{ route('leads.index') }}"
                wire:navigate
                class="inline-flex items-center gap-x-2 text-sm font-semibold text-gray-600 dark:text-neutral-400 hover:text-gray-950 dark:hover:text-white transition-colors"
            >
                ← Voltar
            </a>
        </x-slot:actions>
    </x-page-header>

    <x-card class="p-6 dark:!bg-[#1F2937] dark:!border-gray-700">
        {{ $this->infolist }}
    </x-card>

    <x-filament-actions::modals />

</div>