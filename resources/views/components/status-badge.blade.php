@props(['status', 'color' => 'blue'])

@php
    $colors = [
        'blue'   => 'bg-blue-50 dark:bg-[#295384]/20 text-[#295384] dark:text-blue-300 border-blue-200 dark:border-[#295384]/40',
        'green'  => 'bg-emerald-50 dark:bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 border-emerald-200 dark:border-emerald-500/20',
        'yellow' => 'bg-amber-50 dark:bg-amber-500/10 text-amber-800 dark:text-[#B99B6C] border-amber-200 dark:border-amber-500/20',
        'red'    => 'bg-rose-50 dark:bg-rose-500/10 text-rose-700 dark:text-rose-400 border-rose-200 dark:border-rose-500/20',
        'gray'   => 'bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 border-slate-200 dark:border-slate-700',
    ];

    $colorClass = $colors[$color] ?? $colors['blue'];
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-semibold border uppercase tracking-wider $colorClass"]) }}>
    <span class="w-1.5 h-1.5 rounded-full bg-current mr-1.5"></span>
    {{ $status }}
</span>