<div>
    <x-hero title="Detalhes da Apólice" subtitle="Ficha do contrato de seguro." />

    <div class="mt-6 bg-white dark:bg-gray-900 shadow-xl rounded-2xl p-6 border border-gray-100 dark:border-gray-800">
        {{ $this->infolist }}

        <div class="mt-6 flex flex-wrap items-center justify-end gap-3">
            <a href="{{ route('policies.index') }}" class="px-4 py-2 border border-gray-300 dark:border-gray-700 text-gray-700 dark:text-gray-300 rounded-xl text-sm font-medium hover:bg-gray-50 dark:hover:bg-gray-800 transition">Voltar</a>
            
            <a href="{{ route('policies.document.view', $record) }}" target="_blank" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium rounded-xl transition inline-flex items-center gap-1.5 shadow-xs">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" /></svg>
                Emitir Certificado / PDF
            </a>

            <a href="{{ route('policies.edit', $record) }}" class="px-4 py-2 bg-[#295384] text-white text-sm font-medium rounded-xl hover:bg-[#1f3f66] transition">Editar Apólice</a>
        </div>
    </div>
</div>