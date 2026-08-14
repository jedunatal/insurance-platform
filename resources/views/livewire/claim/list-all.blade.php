<div>
    <x-hero title="Gestão de Sinistros" subtitle="Acompanhamento e registro de sinistros da corretora." />

    <div class="mt-6 flex justify-end">
        <a href="{{ route('claims.create') }}" class="inline-flex items-center px-4 py-2 bg-[#295384] text-white text-sm font-medium rounded-xl hover:bg-[#1f3f66] transition shadow-md">
            Avisar Sinistro
        </a>
    </div>

    <div class="mt-4 bg-white dark:bg-gray-900 shadow-xl rounded-2xl p-6 border border-gray-100 dark:border-gray-800">
        {{ $this->table }}
    </div>
</div>