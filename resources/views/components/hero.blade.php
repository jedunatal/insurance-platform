@props([
    'title',
    'description' => null,
    'badge' => null,
])

<div {{ $attributes->merge(['class' => 'relative overflow-hidden rounded-2xl bg-gradient-to-br from-[#295384] to-[#1c385a] p-8 text-white shadow-md']) }}>
    <div class="relative z-10 max-w-2xl">
        @if($badge)
            <span class="inline-flex items-center rounded-md bg-white/10 px-2.5 py-1 text-xs font-bold tracking-wider uppercase text-[#B99B6C] backdrop-blur-md mb-3 border border-white/10">
                {{ $badge }}
            </span>
        @endif
        <h2 class="text-3xl font-extrabold tracking-tight text-white sm:text-4xl">
            {{ $title }}
        </h2>
        @if($description)
            <p class="mt-2 text-base text-blue-100/80 leading-relaxed">
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