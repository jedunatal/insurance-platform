@props([
    'title',
    'description' => null,
    'badge' => null,
])

<div {{ $attributes->merge(['class' => 'relative overflow-hidden rounded-2xl bg-white dark:bg-[#1F2937] border border-gray-200 dark:border-gray-700/70 p-6 sm:p-8 shadow-xs transition-colors']) }}>
    <div class="relative z-10 max-w-2xl">
        @if($badge)
            <span class="inline-flex items-center rounded-md bg-[#295384]/10 dark:bg-white/10 px-2.5 py-1 text-xs font-bold tracking-wider uppercase text-[#295384] dark:text-[#B99B6C] backdrop-blur-md mb-3 border border-[#295384]/20 dark:border-white/10">
                {{ $badge }}
            </span>
        @endif

        <h2 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-gray-900 dark:text-white">
            {{ $title }}
        </h2>

        @if($description)
            <p class="mt-2 text-sm sm:text-base text-gray-600 dark:text-gray-300 leading-relaxed">
                {{ $description }}
            </p>
        @endif

        @if($slot->isNotEmpty())
            <div class="mt-6 flex flex-wrap items-center gap-4">
                {{ $slot }}
            </div>
        @endif
    </div>
</div>