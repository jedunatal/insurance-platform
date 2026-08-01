<div>
    <x-hero title="Detalhes da Apólice" subtitle="Ficha do contrato de seguro." />

    <div class="mt-6 bg-white dark:bg-gray-900 shadow-xl rounded-2xl p-6 border border-gray-100 dark:border-gray-800">
        {{ $this->infolist }}

        <div class="mt-6 flex justify-end gap-3">
            <a href="{{ route('policies.index') }}" class="px-4 py-2 border border-gray-300 dark:border-gray-700 text-gray-700 dark:text-gray-300 rounded-xl text-sm font-medium">Voltar</a>
            <a href="{{ route('policies.edit', $record) }}" class="px-4 py-2 bg-[#295384] text-white text-sm font-medium rounded-xl hover:bg-[#1f3f66] transition">Editar Apólice</a>
        </div>
    </div>
</div>