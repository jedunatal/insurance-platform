@props([
    'status',
    'color' => 'blue', // blue, green, yellow, red, gray
])

@php
    $colors = [
        'blue'   => 'bg-blue-50 dark:bg-blue-950/40 text-[#295384] dark:text-blue-400 border-blue-200/50 dark:border-blue-800/30',
        'green'  => 'bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-400 border-emerald-200/50 dark:border-emerald-800/30',
        'yellow' => 'bg-amber-50 dark:bg-amber-950/40 text-amber-700 dark:text-amber-400 border-amber-200/50 dark:border-amber-800/30',
        'red'    => 'bg-red-50 dark:bg-red-950/40 text-red-700 dark:text-red-400 border-red-200/50 dark:border-red-800/30',
        'gray'   => 'bg-gray-50 dark:bg-neutral-800 text-gray-600 dark:text-neutral-300 border-gray-200 dark:border-neutral-700',
    ];

    $colorClass = $colors[$color] ?? $colors['blue'];
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold border $colorClass"]) }}>
    {{ $status }}
</span>