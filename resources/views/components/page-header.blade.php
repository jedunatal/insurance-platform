@props([
    'category' => null,
    'title',
    'description' => null,
])

<div {{ $attributes->merge(['class' => 'flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-200 dark:border-slate-800/80 pb-5']) }}>
    <div>
        @if($category)
            <span class="text-[10px] font-bold uppercase tracking-widest text-[#B99B6C]">
                {{ $category }}
            </span>
        @endif
        <h1 class="text-2xl font-extrabold tracking-tight text-slate-900 dark:text-white mt-0.5">
            {{ $title }}
        </h1>
        @if($description)
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                {{ $description }}
            </p>
        @endif
    </div>

    @if(isset($actions))
        <div class="flex items-center gap-3">
            {{ $actions }}
        </div>
    @endif
</div>