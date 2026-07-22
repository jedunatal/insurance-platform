@props([
    'title' => 'Nenhum registro encontrado',
    'description' => 'Não há dados disponíveis para exibição no momento.',
])

<div {{ $attributes->merge(['class' => 'flex flex-col items-center justify-center p-12 text-center rounded-xl border border-dashed border-gray-300 dark:border-neutral-800 bg-gray-50/50 dark:bg-neutral-900/20']) }}>
    @if(isset($icon))
        <div class="p-3 rounded-full bg-gray-100 dark:bg-neutral-800 text-gray-400 dark:text-neutral-500 mb-4">
            {{ $icon }}
        </div>
    @endif

    <h3 class="text-base font-bold text-gray-900 dark:text-white">{{ $title }}</h3>
    <p class="mt-1 text-sm text-gray-500 dark:text-neutral-400 max-w-sm">{{ $description }}</p>

    @if(isset($actions) || $slot->isNotEmpty())
        <div class="mt-6 flex items-center gap-3">
            {{ $actions ?? $slot }}
        </div>
    @endif
</div>