<div class="flex flex-col gap-y-6 w-full max-w-5xl mx-auto px-4 sm:px-6 py-2">

    <x-page-header
        category="Ficha de Atendimento"
        :title="$record->name"
        description="Informações consolidadas e acompanhamento de prospecção."
    >
        <x-slot:actions>
            <div class="flex items-center gap-3">
                <a
                    href="{{ route('leads.index') }}"
                    wire:navigate
                    class="inline-flex items-center gap-x-2 text-sm font-semibold text-gray-600 dark:text-neutral-400 hover:text-gray-950 dark:hover:text-white transition-colors"
                >
                    ← Voltar
                </a>

                @if($record->status?->value !== 'Convertido' && $record->status !== 'Convertido')
                    <a
                        href="{{ route('insureds.create', ['lead_id' => $record->id]) }}"
                        wire:navigate
                        class="inline-flex items-center gap-x-1.5 px-3 py-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold shadow-xs transition"
                    >
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7.5v3m0 0v3m0-3h3m-3 0h-3m-2.25-4.125a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0ZM3 19.235v-.11a6.375 6.375 0 0 1 12.75 0v.109A12.318 12.318 0 0 1 9.374 21c-2.331 0-4.512-.645-6.374-1.766Z"/></svg>
                        Converter em Segurado
                    </a>
                @endif
            </div>
        </x-slot:actions>
    </x-page-header>

    <x-card class="p-6 dark:!bg-[#1F2937] dark:!border-gray-700">
        {{ $this->infolist }}
    </x-card>

    <x-filament-actions::modals />

</div>