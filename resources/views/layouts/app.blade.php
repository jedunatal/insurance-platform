<!DOCTYPE html>
<html class="h-full bg-slate-100 dark:bg-[#080c14] text-slate-900 dark:text-slate-100 antialiased selection:bg-[#295384] selection:text-white" lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Insurance Platform' }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

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
        applyTheme();
        document.addEventListener('livewire:navigated', applyTheme);
    </script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @filamentStyles

    <style>
        [x-cloak] { display: none !important; }
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        :root {
            --color-primary: #295384;
            --color-secondary: #B99B6C;
        }
    </style>
</head>

<body
    class="h-full bg-slate-100 dark:bg-[#080c14] text-slate-900 dark:text-slate-100 relative overflow-x-hidden transition-colors duration-200"
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
    @keydown.escape.window="searchOpen = false; mobileSidebarOpen = false"
>

    {{-- Fundo Radial Suave para Light e Dark --}}
    <div class="fixed inset-0 bg-[radial-gradient(ellipse_70%_50%_at_50%_0%,rgba(41,83,132,0.08),rgba(255,255,255,0))] dark:bg-[radial-gradient(ellipse_70%_50%_at_50%_0%,rgba(41,83,132,0.22),rgba(255,255,255,0))] pointer-events-none"></div>

    {{-- Layout Flutuante --}}
    <div class="flex h-screen overflow-hidden p-3 sm:p-4 gap-4 relative z-10">
        
        {{-- Side Dock (Sidebar) --}}
        <aside 
            class="hidden lg:flex flex-col bg-white/80 dark:bg-slate-900/60 border border-slate-200/90 dark:border-slate-800/80 rounded-3xl shadow-xl dark:shadow-2xl backdrop-blur-2xl transition-all duration-300 p-3 shrink-0"
            :class="sidebarCollapsed ? 'w-20' : 'w-64'"
        >
            {{-- Header da Marca --}}
            <div class="flex items-center gap-3 px-3 py-3 mb-4 border-b border-slate-200/80 dark:border-slate-800/60">
                <div class="w-10 h-10 rounded-2xl bg-[#295384] flex items-center justify-center text-white font-extrabold text-xs shadow-md shrink-0">
                    IP
                </div>
                <div x-show="!sidebarCollapsed" x-transition class="truncate">
                    <h1 class="text-xs font-bold text-slate-900 dark:text-white tracking-wider uppercase">Insurance</h1>
                    <p class="text-[10px] text-[#B99B6C] font-extrabold tracking-widest uppercase">PLATFORM</p>
                </div>
            </div>

            {{-- Links do Menu --}}
            <nav class="flex-1 overflow-y-auto space-y-1">
                @include('components.sidebar-navigation')
            </nav>

            {{-- Perfil do Usuário --}}
            <div class="pt-3 border-t border-slate-200/80 dark:border-slate-800/60 flex items-center gap-3 px-2">
                <div class="w-9 h-9 rounded-xl bg-[#295384] flex items-center justify-center text-white font-bold text-xs shrink-0 shadow-md">
                    JE
                </div>
                <div x-show="!sidebarCollapsed" x-transition class="truncate leading-tight">
                    <p class="text-xs font-bold text-slate-900 dark:text-white truncate">Jorge Eduardo</p>
                    <p class="text-[10px] text-slate-500 dark:text-slate-400 truncate">Consultor Senior</p>
                </div>
            </div>
        </aside>

        {{-- Canvas do Conteúdo Principal --}}
        <main class="flex-1 flex flex-col min-w-0 overflow-hidden bg-white/80 dark:bg-slate-900/40 border border-slate-200/90 dark:border-slate-800/60 rounded-3xl shadow-xl dark:shadow-2xl backdrop-blur-md">
            
            {{-- Topbar --}}
            <header class="h-16 px-6 border-b border-slate-200/80 dark:border-slate-800/60 flex items-center justify-between shrink-0">
                <div class="flex items-center gap-3">
                    <button @click="mobileSidebarOpen = true" class="lg:hidden p-2 rounded-xl text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800/60 transition">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    </button>

                    <button @click="toggleSidebar()" class="hidden lg:flex p-2 rounded-xl text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800/60 transition">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" /></svg>
                    </button>
                    
                    {{-- Busca Integrada --}}
                    <div class="relative w-64 sm:w-80 hidden sm:block">
                        <svg class="w-4 h-4 text-slate-400 dark:text-slate-500 absolute left-3.5 top-1/2 -translate-y-1/2" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 100 11 5.5 5.5 0 000-11zM2 9a7 7 0 1112.452 4.391l3.328 3.329a.75.75 0 11-1.06 1.06l-3.329-3.328A7 7 0 012 9z" clip-rule="evenodd" /></svg>
                        <input class="w-full pl-9 pr-4 py-1.5 bg-slate-100 dark:bg-slate-950/60 border border-slate-200 dark:border-slate-800/80 rounded-xl text-xs text-slate-900 dark:text-white placeholder:text-slate-400 dark:placeholder:text-slate-500 focus:outline-none focus:border-[#295384] transition" placeholder="Buscar apólices, clientes ou leads...">
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    {{-- Botão de Alternar Tema (Claro/Escuro) --}}
                    <button type="button" @click="toggleTheme()" class="p-2 rounded-xl text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800/60 transition" title="Alternar Tema">
                        <svg x-show="isDark" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v2.25m0 13.5V21M4.313 4.313l1.591 1.591M16.5 16.5l1.591 1.591M21 12h-2.25m-13.5 0H3m16.5-7.687l-1.591 1.591M6.719 17.281l-1.591 1.591M15 12a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                        <svg x-show="!isDark" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21.75 12.42a5.25 5.25 0 01-5.4 5.39A8.25 8.25 0 017.3 6.3a5.25 5.25 0 015.4-5.38 8.25 8.25 0 009.05 11.5z" /></svg>
                    </button>

                    <div class="h-5 w-px bg-slate-200 dark:bg-slate-800"></div>

                    {{-- Badge Status --}}
                    <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-blue-50 dark:bg-[#295384]/20 border border-blue-200 dark:border-[#295384]/40 text-[#295384] dark:text-[#B99B6C] text-xs font-semibold">
                        <span class="w-1.5 h-1.5 rounded-full bg-[#295384] dark:bg-[#B99B6C] animate-pulse"></span>
                        Corretora Online
                    </span>
                </div>
            </header>

            {{-- Conteúdo das Páginas --}}
            <div class="flex-1 overflow-y-auto p-6 space-y-6">
                @isset($header)
                    <div>
                        {{ $header }}
                    </div>
                @endisset

                {{ $slot ?? '' }}
                @yield('content')
            </div>
        </main>
    </div>

    {{-- Drawer Mobile --}}
    <div x-show="mobileSidebarOpen" x-cloak class="relative z-50 lg:hidden" role="dialog" aria-modal="true">
        <div class="fixed inset-0 bg-slate-950/80 backdrop-blur-sm transition-opacity"></div>
        <div class="fixed inset-0 flex">
            <div class="relative flex w-full max-w-xs flex-1 flex-col bg-white dark:bg-slate-900 p-6 border-r border-slate-200 dark:border-slate-800" @click.away="mobileSidebarOpen = false">
                <div class="flex items-center justify-between mb-6">
                    <span class="text-base font-bold text-slate-900 dark:text-white">InsurancePlatform</span>
                    <button @click="mobileSidebarOpen = false" class="text-slate-400 hover:text-slate-900 dark:hover:text-white">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>
                <nav class="flex-1 overflow-y-auto">
                    @include('components.sidebar-navigation')
                </nav>
            </div>
        </div>
    </div>

    @livewire('notifications')
    @filamentScripts
    @stack('scripts')
</body>
</html>