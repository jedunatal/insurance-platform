@props([
    'title' => null,
    'description' => null,
])

<div {{ $attributes->merge(['class' => 'bg-white dark:bg-neutral-950 border border-gray-200 dark:border-neutral-800 rounded-xl p-6 shadow-xs']) }}>
    @if($title || isset($headerActions))
        <div class="border-b border-gray-100 dark:border-neutral-900 pb-4 mb-5 flex items-center justify-between">
            <div>
                @if($title)
                    <h3 class="text-lg font-bold text-gray-950 dark:text-white">{{ $title }}</h3>
                @endif
                @if($description)
                    <p class="text-xs text-gray-500 dark:text-neutral-400 mt-0.5">{{ $description }}</p>
                @endif
            </div>
            @if(isset($headerActions))
                <div>{{ $headerActions }}</div>
            @endif
        </div>
    @endif

    {{ $slot }}

    @if(isset($footer))
        <div class="border-t border-gray-100 dark:border-neutral-900 pt-4 mt-5">
            {{ $footer }}
        </div>
    @endif
</div>