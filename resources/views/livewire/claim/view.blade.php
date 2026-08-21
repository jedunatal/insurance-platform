<div class="flex flex-col gap-y-6 w-full max-w-5xl mx-auto px-4 sm:px-6 py-2">

    <x-page-header
        category="Ficha de Sinistro"
        :title="$record->claim_number"
        description="Ficha técnica e andamento da ocorrência."
    >
        <x-slot:actions>
            <a href="{{ route('claims.edit', $record) }}" wire:navigate class="inline-flex items-center px-3.5 py-2 text-sm font-semibold text-white bg-[#295384] rounded-lg hover:bg-[#1f3f64] transition-colors">
                Editar Sinistro
            </a>
            <a href="{{ route('claims.index') }}" wire:navigate class="inline-flex items-center gap-x-2 text-sm font-semibold text-gray-600 dark:text-neutral-400 hover:text-gray-950 dark:hover:text-white transition-colors">
                ← Voltar
            </a>
        </x-slot:actions>
    </x-page-header>

    <x-card class="p-6 dark:!bg-[#1F2937] dark:!border-gray-700">
        {{ $this->infolist }}
    </x-card>

    <x-card class="p-6 dark:!bg-[#1F2937] dark:!border-gray-700">
        <livewire:ged.attachment-manager :model="$record" :key="'ged-claim-'.$record->id" />
    </x-card>

    <x-filament-actions::modals />

</div>
