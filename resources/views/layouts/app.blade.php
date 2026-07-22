<!DOCTYPE html>
<html class="min-h-screen bg-gray-100 dark:bg-[#121827]" lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Insurance Platform' }}</title>

    {{-- Script de Tema Reativo (Suporta Carga Inicial + Livewire wire:navigate) --}}
    <script>
        const applyTheme = () => {
            const theme = localStorage.getItem('theme');
            const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;

            if (theme === 'dark' || (!theme && prefersDark)) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        };

        // Aplica imediatamente antes da renderização da página
        applyTheme();

        // Re-aplica automaticamente a cada navegação SPA do Livewire
        document.addEventListener('livewire:navigated', applyTheme);
    </script>

    {{-- Assets do Projeto via Vite --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Estilos Globais do Filament Forms & Actions --}}
    @filamentStyles

    <style>
        [x-cloak] { display: none !important; }
        :root {
            --color-primary: #295384;
            --color-secondary: #B99B6C;
        }
    </style>
</head>

<body
    class="min-h-screen bg-gray-100 dark:bg-[#121827] text-gray-900 dark:text-gray-100 transition-colors duration-200"
    x-data="{
        sidebarCollapsed: localStorage.getItem('sidebarCollapsed') === 'true',
        searchOpen: false,
        mobileSidebarOpen: false,
        isDark: localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches),
        toggleSidebar() {
            this.sidebarCollapsed = !this.sidebarCollapsed;
            localStorage.setItem('sidebarCollapsed', this.sidebarCollapsed);
        },
        toggleMobileSearch() {
            this.searchOpen = !this.searchOpen;
        },
        toggleTheme() {
            this.isDark = !this.isDark;
            if (this.isDark) {
                document.documentElement.classList.add('dark');
                localStorage.setItem('theme', 'dark');
            } else {
                document.documentElement.classList.remove('dark');
                localStorage.setItem('theme', 'light');
            }
        }
    }"
    @keydown.escape.window="searchOpen = false"
>

{{-- Top bar --}}
<header class="fixed top-0 inset-x-0 z-50 border-b border-gray-200 bg-white dark:border-neutral-800 dark:bg-[#1F2937] transition-colors duration-200 shadow-xs">
    <div class="relative flex h-14 items-center justify-between px-4 sm:px-6">
        
        {{-- Hambúrguer + Botão Recolher Menu --}}
        <div class="flex items-center gap-x-3">
            <button type="button"
                    @click="mobileSidebarOpen = true"
                    class="-m-2.5 p-2.5 text-gray-500 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white lg:hidden">
                <span class="sr-only">Abrir menu</span>
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" /></svg>
            </button>
            <button type="button"
                    id="sidebar-toggle"
                    @click="toggleSidebar()"
                    class="hidden lg:inline-flex -m-2.5 p-2.5 text-gray-500 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white"
                    :title="sidebarCollapsed ? 'Expandir menu' : 'Recolher menu'">
                <span class="sr-only">Alternar menu</span>
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" /></svg>
            </button>

            <div aria-hidden="true" class="hidden lg:block h-6 w-px bg-gray-200 dark:bg-neutral-800"></div>

            <a href="{{ route('dashboard') }}" class="hidden lg:flex items-center shrink-0 gap-2">
                <div class="w-8 h-8 rounded-lg bg-[#295384] flex items-center justify-center text-white font-bold text-sm">IP</div>
                <span class="text-md font-bold tracking-tight text-gray-950 dark:text-white">Insurance<span class="text-[#B99B6C]">Platform</span></span>
            </a>
        </div>

        {{-- Logo Mobile centralizado --}}
        <div class="lg:hidden pointer-events-none absolute inset-0 flex items-center justify-center">
            <a href="{{ route('dashboard') }}" class="pointer-events-auto flex items-center gap-1">
                <span class="text-sm font-bold tracking-tight text-gray-950 dark:text-white">Insurance<span class="text-[#B99B6C]">Platform</span></span>
            </a>
        </div>

        {{-- Barra de busca centralizada --}}
        <div class="hidden lg:flex flex-1 items-center justify-center px-4">
            <div class="w-full max-w-2xl relative">
                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                    <svg class="h-5 w-5 text-gray-400 dark:text-gray-500" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 100 11 5.5 5.5 0 000-11zM2 9a7 7 0 1112.452 4.391l3.328 3.329a.75.75 0 11-1.06 1.06l-3.329-3.328A7 7 0 012 9z" clip-rule="evenodd" /></svg>
                </div>
                <input class="block w-full rounded-md border-0 py-1.5 pl-10 pr-3 bg-white dark:bg-neutral-900 text-gray-900 dark:text-white ring-1 ring-inset ring-gray-300 dark:ring-neutral-800 placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:ring-2 focus:ring-inset focus:ring-blue-600 sm:text-sm/6" placeholder="Buscar apólices, clientes ou leads...">
            </div>
        </div>

        {{-- Área Direita das Ações --}}
        <div class="flex items-center gap-x-1 sm:gap-x-2">
            <div class="hidden lg:flex items-center gap-x-2">
                
                {{-- Botão Dark Mode Reativo Inteligente --}}
                <button 
                    type="button" 
                    @click="toggleTheme()" 
                    class="relative inline-flex h-9 w-9 items-center justify-center rounded-full text-gray-500 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-neutral-900 transition-colors"
                    title="Alternar Tema"
                >
                    <svg x-show="isDark" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2.25m0 13.5V21M4.313 4.313l1.591 1.591M16.5 16.5l1.591 1.591M21 12h-2.25m-13.5 0H3m16.5-7.687l-1.591 1.591M6.719 17.281l-1.591 1.591M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    <svg x-show="!isDark" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 12.42a5.25 5.25 0 01-5.4 5.39A8.25 8.25 0 017.3 6.3a5.25 5.25 0 015.4-5.38 8.25 8.25 0 009.05 11.5z" />
                    </svg>
                </button>

                <button type="button" class="relative inline-flex h-9 w-9 items-center justify-center rounded-full text-gray-500 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-neutral-900 transition">
                    <span class="absolute top-2 right-2 block h-1.5 w-1.5 rounded-full bg-red-500"></span>
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" /></svg>
                </button>
                <div aria-hidden="true" class="h-6 w-px bg-gray-200 dark:bg-neutral-800 mx-1"></div>
            </div>

            {{-- Dropdown de Perfil --}}
            <div x-data="{ userMenuOpen: false }" class="relative">
                <button @click="userMenuOpen = !userMenuOpen" class="flex items-center gap-x-3 rounded-full p-1 hover:bg-gray-50 dark:hover:bg-neutral-900 transition">
                    <div class="w-9 h-9 rounded-full bg-[#295384] flex items-center justify-center text-white font-semibold text-sm">
                        JE
                    </div>
                    <span class="hidden lg:flex lg:flex-col lg:items-start lg:leading-tight">
                        <span class="text-sm font-semibold text-gray-900 dark:text-white">Jorge Eduardo</span>
                        <span class="text-xs text-gray-500 dark:text-neutral-400">Consultor Senior</span>
                    </span>
                </button>
                <div x-cloak x-show="userMenuOpen" @click.away="userMenuOpen = false" class="absolute right-0 z-10 mt-2 w-56 origin-top-right rounded-md bg-white dark:bg-neutral-950 py-2 shadow-lg ring-1 ring-black ring-opacity-5 dark:ring-neutral-800 focus:outline-none">
                    <div class="px-3 py-2 border-b border-gray-200 dark:border-neutral-800">
                        <p class="text-sm font-semibold text-gray-900 dark:text-white">Jorge Eduardo</p>
                        <p class="text-xs text-gray-500 dark:text-neutral-400 mt-1">jorge@corretora.com</p>
                    </div>
                    <a href="#" class="block w-full text-left px-3 py-2 text-sm text-gray-900 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-neutral-900">Configurações</a>
                    <a href="#" class="block w-full text-left px-3 py-2 text-sm text-gray-900 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-neutral-900">Sair</a>
                </div>
            </div>
        </div>
    </div>

    {{-- Mobile action row --}}
    <div class="lg:hidden flex items-center justify-center gap-x-6 border-t border-gray-200 dark:border-neutral-800 px-4 py-2">
        <button type="button" @click="toggleMobileSearch()" class="text-gray-500 dark:text-neutral-400"><svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.604 10.604z" /></svg></button>
        
        {{-- Botão Dark Mode Mobile --}}
        <button type="button" @click="toggleTheme()" class="text-gray-500 dark:text-neutral-400">
            <svg x-show="isDark" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2.25m0 13.5V21M4.313 4.313l1.591 1.591M16.5 16.5l1.591 1.591M21 12h-2.25m-13.5 0H3m16.5-7.687l-1.591 1.591M6.719 17.281l-1.591 1.591M15 12a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
            <svg x-show="!isDark" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 12.42a5.25 5.25 0 01-5.4 5.39A8.25 8.25 0 017.3 6.3a5.25 5.25 0 015.4-5.38 8.25 8.25 0 009.05 11.5z" /></svg>
        </button>
        
        <button type="button" class="text-gray-500 dark:text-neutral-400"><svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" /></svg></button>
    </div>

    {{-- Painel de busca móvel --}}
    <div x-show="searchOpen" x-cloak class="lg:hidden border-t border-gray-200 dark:border-neutral-800 bg-white dark:bg-neutral-950 px-4 py-3">
        <input class="w-full rounded-md border-gray-300 dark:border-neutral-800 bg-gray-50 dark:bg-neutral-900 text-sm" placeholder="Buscar no sistema...">
    </div>
</header>

{{-- Mobile Sidebar --}}
<div x-show="mobileSidebarOpen" x-cloak class="relative z-50 lg:hidden" role="dialog" aria-modal="true">
    <div class="fixed inset-0 bg-gray-900/80 transition-opacity"></div>
    <div class="fixed inset-0 flex">
        <div class="relative flex w-full max-w-xs flex-1 flex-col bg-white dark:bg-neutral-950 px-6 pb-4 pt-5" @click.away="mobileSidebarOpen = false">
            <div class="absolute top-0 left-full flex w-16 justify-center pt-5">
                <button type="button" @click="mobileSidebarOpen = false" class="-m-2.5 p-2.5">
                    <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>
            <div class="flex h-12 items-center">
                <span class="text-lg font-bold text-gray-900 dark:text-white">InsurancePlatform</span>
            </div>
            <nav class="mt-4 flex flex-1 flex-col">
                @include('components.sidebar-navigation')
            </nav>
        </div>
    </div>
</div>

{{-- Sidebar Estática para Desktop --}}
{{-- Troque dark:bg-neutral-950 por dark:bg-[#1F2937] --}}
<div
    class="hidden lg:fixed lg:top-14 lg:bottom-0 lg:left-0 lg:z-40 lg:flex lg:flex-col transition-[width] duration-200 ease-in-out border-r border-gray-200 dark:border-neutral-800 bg-white dark:bg-[#1F2937] py-4"
    :class="sidebarCollapsed ? 'lg:w-16 px-2' : 'lg:w-60 px-6'"
>
    <nav class="flex flex-1 flex-col">
        @include('components.sidebar-navigation')
    </nav>
</div>

{{-- Área do Conteúdo Principal --}}
<div
    class="w-full pt-28 lg:pt-14 transition-[padding] duration-200 ease-in-out"
    :class="sidebarCollapsed ? 'lg:pl-16' : 'lg:pl-60'"
>
    <main class="py-6">
        <div class="w-full px-4 sm:px-6 lg:px-8 flex flex-col gap-y-4">
            
            {{-- Slot do Título/Header --}}
            @isset($header)
                <div class="mb-2">
                    {{ $header }}
                </div>
            @endisset

            {{-- Injeção das Views Secundárias --}}
            {{ $slot ?? '' }}
            @yield('content')
            
        </div>
    </main>
</div>

{{-- 🔔 Notificações e Modais Globais do Filament --}}
@livewire('notifications')

{{-- ⚡ Scripts Globais do Filament --}}
@filamentScripts

</body>
</html>