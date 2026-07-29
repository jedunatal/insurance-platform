@props(['cancelUrl' => null, 'submitText' => 'Salvar'])

<div {{ $attributes->merge(['class' => 'flex items-center justify-end gap-x-4 border-t border-slate-100 dark:border-slate-800/80 pt-5 mt-6']) }}>
    @if($cancelUrl)
        <a href="{{ $cancelUrl }}" wire:navigate class="text-xs font-semibold text-slate-600 hover:text-slate-900 dark:text-slate-400 dark:hover:text-white transition">
            Cancelar
        </a>
    @endif

    <button type="submit" class="inline-flex justify-center rounded-xl bg-[#295384] hover:bg-[#1f3f64] px-5 py-2.5 text-xs font-bold text-white shadow-md transition-all active:scale-95">
        {{ $submitText }}
    </button>
</div>