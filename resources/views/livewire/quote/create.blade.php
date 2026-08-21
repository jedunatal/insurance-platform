<div class="space-y-6">
    <x-slot:header>
        <x-hero
            badge="Novo Estudo"
            title="Criar Cotação Multi-Seguradoras"
            description="Cadastre as opções cotadas nas seguradoras parceiras para gerar o comparativo."
            icon="heroicon-o-plus-circle"
        />
    </x-slot:header>

    <form wire:submit="create" class="space-y-6">
        {{ $this->form }}

        <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-200 dark:border-slate-800">
            <a href="{{ route('quotes.index') }}" class="px-4 py-2 border border-slate-300 dark:border-slate-700 text-slate-700 dark:text-slate-300 rounded-xl text-xs font-semibold hover:bg-slate-50 dark:hover:bg-slate-800 transition">
                Cancelar
            </a>
            <button type="submit" class="px-5 py-2 bg-[#295384] hover:bg-[#1c385a] text-white rounded-xl text-xs font-semibold shadow-xs transition">
                Salvar e Gerar Comparativo
            </button>
        </div>
    </form>
</div>
