@props(['title' => null, 'description' => null])

<div
    {{ $attributes->merge(['class' => 'bg-white dark:bg-slate-900/60 border border-slate-200 dark:border-slate-800/80 rounded-2xl p-6 shadow-sm dark:shadow-xl backdrop-blur-md transition-all overflow-visible']) }}>
    @if ($title || isset($headerActions))
        <div class="border-b border-slate-100 dark:border-slate-800/60 pb-4 mb-5 flex items-center justify-between">
            <div>
                @if ($title)
                    <h3 class="text-sm font-bold text-slate-900 dark:text-white uppercase tracking-wider">
                        {{ $title }}</h3>
                @endif
                @if ($description)
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 font-normal leading-relaxed">
                        {{ $description }}</p>
                @endif
            </div>
            @if (isset($headerActions))
                <div>{{ $headerActions }}</div>
            @endif
        </div>
    @endif

    {{ $slot }}

    @if (isset($footer))
        <div class="border-t border-slate-100 dark:border-slate-800/60 pt-4 mt-5">
            {{ $footer }}
        </div>
    @endif
</div>
