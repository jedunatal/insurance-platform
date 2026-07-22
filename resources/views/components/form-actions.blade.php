@props([
    'cancelUrl' => null,
    'submitText' => 'Salvar',
])

<div {{ $attributes->merge(['class' => 'flex items-center justify-end gap-x-4 border-t border-gray-100 dark:border-neutral-900 pt-5']) }}>
    @if($cancelUrl)
        <a href="{{ $cancelUrl }}" wire:navigate class="text-sm font-semibold text-gray-700 dark:text-neutral-300 hover:underline">
            Cancelar
        </a>
    @endif

    <button type="submit" class="inline-flex justify-center rounded-lg bg-[#295384] hover:bg-opacity-90 px-4 py-2.5 text-sm font-bold text-white shadow-xs transition-colors">
        {{ $submitText }}
    </button>
</div>