@props([
    'title',
    'description' => null,
    'badge' => null,
    'subtitle' => null,
    'icon' => 'heroicon-o-squares-2x2',
])

@php
    $badgeText = $badge ?? $subtitle;
@endphp

<div
    {{ $attributes->merge([
        'class' => 'relative overflow-hidden rounded-3xl border border-slate-200/70 dark:border-slate-700/70 bg-gradient-to-br from-white via-slate-50 to-slate-100 dark:from-slate-900 dark:via-slate-900 dark:to-slate-800 shadow-xl shadow-slate-200/40 dark:shadow-black/20'
    ]) }}
>

    {{-- Background --}}
    <div class="absolute inset-0 overflow-hidden pointer-events-none">

        <div class="absolute -top-24 -right-24 h-72 w-72 rounded-full bg-[#295384]/10 blur-3xl"></div>

        <div class="absolute -bottom-24 -left-24 h-64 w-64 rounded-full bg-[#B99B6C]/10 blur-3xl"></div>

        <div
            class="absolute inset-0 opacity-[0.03]"
            style="
                background-image:
                    linear-gradient(to right,#295384 1px,transparent 1px),
                    linear-gradient(to bottom,#295384 1px,transparent 1px);
                background-size:40px 40px;
            "
        ></div>

    </div>

    <div class="relative p-8">

        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-8">

            {{-- ESQUERDA --}}
            <div class="flex items-start gap-5 flex-1">

                <div
                    class="
                        flex
                        h-16
                        w-16
                        items-center
                        justify-center
                        rounded-2xl
                        bg-gradient-to-br
                        from-[#295384]
                        to-[#3E6EA1]
                        text-white
                        shadow-lg
                        shadow-[#295384]/20
                        shrink-0
                    "
                >
                    <x-dynamic-component
                        :component="$icon"
                        class="w-8 h-8"
                    />
                </div>

                <div class="space-y-3">

                    @if($badgeText)

                        <div
                            class="
                                inline-flex
                                items-center
                                gap-2
                                rounded-full
                                border
                                border-[#295384]/20
                                bg-[#295384]/5
                                px-3
                                py-1
                                text-[11px]
                                font-bold
                                uppercase
                                tracking-[0.18em]
                                text-[#295384]
                                dark:border-[#B99B6C]/20
                                dark:bg-[#B99B6C]/10
                                dark:text-[#B99B6C]
                            "
                        >

                            <span
                                class="h-2 w-2 rounded-full bg-[#295384] dark:bg-[#B99B6C]"
                            ></span>

                            {{ $badgeText }}

                        </div>

                    @endif

                    <h1
                        class="
                            text-3xl
                            font-black
                            tracking-tight
                            text-slate-900
                            dark:text-white
                        "
                    >
                        {{ $title }}
                    </h1>

                    @if($description)

                        <p
                            class="
                                max-w-3xl
                                text-sm
                                leading-7
                                text-slate-600
                                dark:text-slate-300
                            "
                        >
                            {{ $description }}
                        </p>

                    @endif

                </div>

            </div>

            {{-- DIREITA --}}
            <div
                class="
                    flex
                    flex-col
                    items-end
                    gap-4
                "
            >

                @if(isset($slot) && $slot->isNotEmpty())

                    <div class="flex flex-wrap justify-end gap-3">

                        {{ $slot }}

                    </div>

                @endif

            </div>

        </div>

    </div>

</div>