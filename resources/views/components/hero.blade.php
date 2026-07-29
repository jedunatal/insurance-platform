@props([
    'title',
    'description' => null,
    'badge' => null,
])

<div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4 pb-4 border-b border-slate-200 dark:border-slate-800/80 mb-2">
    <div class="space-y-1">
        @if($badge)
            <div class="inline-flex items-center gap-1.5 px-3 py-0.5 rounded-full bg-blue-50 dark:bg-[#295384]/20 border border-blue-200 dark:border-[#295384]/40 text-[#295384] dark:text-[#B99B6C] text-[10px] font-bold uppercase tracking-wider">
                <span class="w-1.5 h-1.5 rounded-full bg-[#295384] dark:bg-[#B99B6C]"></span>
                {{ $badge }}
            </div>
        @endif

        <h1 class="text-3xl font-extrabold text-slate-900 dark:text-white tracking-tight">
            {{ $title }}
        </h1>

        @if($description)
            <p class="text-xs text-slate-600 dark:text-slate-400 font-normal leading-relaxed max-w-2xl">
                {{ $description }}
            </p>
        @endif
    </div>

    @if($slot->isNotEmpty())
        <div class="flex items-center gap-3 shrink-0">
            {{ $slot }}
        </div>
    @endif
</div>