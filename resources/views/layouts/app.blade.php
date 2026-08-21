<!DOCTYPE html>
<html class="h-full bg-slate-100 dark:bg-[#080c14] text-slate-900 dark:text-slate-100 antialiased selection:bg-[#295384] selection:text-white" lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Painel' }} | Salut Royale</title>

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
        
        {{-- Side Dock (Sidebar Desktop) --}}
        <aside 
            class="hidden lg:flex flex-col bg-white/80 dark:bg-slate-900/60 border border-slate-200/90 dark:border-slate-800/80 rounded-3xl shadow-xl dark:shadow-2xl backdrop-blur-2xl transition-all duration-300 p-3 shrink-0"
            :class="sidebarCollapsed ? 'w-20' : 'w-64'"
        >
            {{-- Header da Marca --}}
            <div class="flex items-center gap-3 px-3 py-3 mb-4 border-b border-slate-200/80 dark:border-slate-800/60">
                <div class="w-10 h-10 rounded-2xl bg-gradient-to-br from-[#295384] to-[#1a365d] flex items-center justify-center text-[#B99B6C] font-extrabold text-xs shadow-md shrink-0 ring-2 ring-[#B99B6C]/20">
                    SR
                </div>
                <div x-show="!sidebarCollapsed" x-transition class="truncate">
                    <h1 class="text-xs font-bold text-slate-900 dark:text-white tracking-wider uppercase">SALUT</h1>
                    <p class="text-[10px] text-[#B99B6C] font-extrabold tracking-widest uppercase">ROYALE</p>
                </div>
            </div>

            {{-- Links do Menu --}}
            <nav class="flex-1 overflow-y-auto space-y-1">
                @include('components.sidebar-navigation')
            </nav>

            {{-- Resumo do Perfil na Sidebar Rodapé --}}
            <div class="pt-3 border-t border-slate-200/80 dark:border-slate-800/60 flex items-center gap-3 px-2">
                <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-[#295384] to-[#1a365d] flex items-center justify-center text-[#B99B6C] font-bold text-xs shrink-0 shadow-md">
                    SR
                </div>
                <div x-show="!sidebarCollapsed" x-transition class="truncate leading-tight">
                    <p class="text-xs font-bold text-slate-900 dark:text-white truncate">Salut Royale</p>
                    <p class="text-[10px] text-slate-500 dark:text-slate-400 truncate">Corretora de Seguros</p>
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
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" /></svg>
                    </button>
                    
                    {{-- Busca Global Integrada --}}
                    <div class="hidden sm:block">
                        <livewire:layout.global-search />
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    {{-- Central de Alertas e Notificações --}}
                    <livewire:layout.notification-bell />

                    {{-- Botão de Alternar Tema (Claro/Escuro) --}}
                    <button type="button" @click="toggleTheme()" class="p-2 rounded-xl text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800/60 transition" title="Alternar Tema">
                        <svg x-show="isDark" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v2.25m0 13.5V21M4.313 4.313l1.591 1.591M16.5 16.5l1.591 1.591M21 12h-2.25m-13.5 0H3m16.5-7.687l-1.591 1.591M6.719 17.281l-1.591 1.591M15 12a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                        <svg x-show="!isDark" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21.75 12.42a5.25 5.25 0 01-5.4 5.39A8.25 8.25 0 017.3 6.3a5.25 5.25 0 015.4-5.38 8.25 8.25 0 009.05 11.5z" /></svg>
                    </button>

                    <div class="h-5 w-px bg-slate-200 dark:bg-slate-800 hidden sm:block"></div>

                    {{-- 👤 Dropdown de Perfil Interativo --}}
                    <div x-data="{ userMenuOpen: false }" class="relative">
                        <button 
                            @click="userMenuOpen = !userMenuOpen" 
                            type="button" 
                            class="flex items-center gap-2.5 p-1.5 rounded-2xl hover:bg-slate-100 dark:hover:bg-slate-800/60 transition text-left focus:outline-none"
                        >
                            <div class="w-8 h-8 rounded-xl bg-gradient-to-br from-[#295384] to-[#1f3f66] flex items-center justify-center text-white font-bold text-xs shrink-0 shadow-md">
                                {{ strtoupper(substr(auth()->user()->name ?? 'CO', 0, 2)) }}
                            </div>
                            <div class="hidden md:flex flex-col leading-tight">
                                <span class="text-xs font-bold text-slate-900 dark:text-white">{{ auth()->user()->name ?? 'Corretor' }}</span>
                                <span class="text-[10px] text-slate-500 dark:text-slate-400">{{ auth()->user()?->roleTitle() ?? 'Corretor Gestor' }}</span>
                            </div>
                            <svg class="w-4 h-4 text-slate-400 hidden md:block transition-transform duration-200" :class="userMenuOpen ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                            </svg>
                        </button>

                        {{-- Menu Suspenso --}}
                        <div 
                            x-cloak 
                            x-show="userMenuOpen" 
                            @click.away="userMenuOpen = false"
                            x-transition:enter="transition ease-out duration-100"
                            x-transition:enter-start="transform opacity-0 scale-95"
                            x-transition:enter-end="transform opacity-100 scale-100"
                            x-transition:leave="transition ease-in duration-75"
                            x-transition:leave-start="transform opacity-100 scale-100"
                            x-transition:leave-end="transform opacity-0 scale-95"
                            class="absolute right-0 z-50 mt-2 w-60 origin-top-right rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 p-2 shadow-xl dark:shadow-2xl focus:outline-none"
                        >
                            <div class="px-3 py-2 border-b border-slate-100 dark:border-slate-800">
                                <p class="text-xs font-bold text-slate-900 dark:text-white truncate">{{ auth()->user()->name ?? 'Corretor' }}</p>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5 truncate">{{ auth()->user()->email ?? 'corretor@corretora.com' }}</p>
                                <div class="mt-1.5 flex items-center gap-1.5">
                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-bold bg-[#295384]/10 text-[#295384] dark:text-blue-300">
                                        🏢 {{ auth()->user()->tenant?->name ?? 'Minha Corretora' }}
                                    </span>
                                </div>
                            </div>

                            <div class="pt-1.5 space-y-0.5">
                                @if (auth()->user()?->canManageTeam())
                                    <a href="{{ route('settings.team') }}" wire:navigate class="flex items-center gap-2 px-3 py-2 text-xs font-semibold text-slate-700 dark:text-slate-300 rounded-xl hover:bg-slate-100 dark:hover:bg-slate-800/60 transition">
                                        <svg class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                        <span>Gestão da Equipe</span>
                                    </a>
                                @endif

                                <form action="{{ route('logout') }}" method="POST" class="w-full">
                                    @csrf
                                    <button type="submit" class="w-full flex items-center gap-2 px-3 py-2 text-xs font-semibold text-rose-600 dark:text-rose-400 rounded-xl hover:bg-rose-50 dark:hover:bg-rose-950/30 transition text-left cursor-pointer">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75"/></svg>
                                        <span>Sair da Conta</span>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                </div>
            </header>

            {{-- Área do Conteúdo com Flexbox para o Footer empurrado pro final --}}
            <div class="flex-1 overflow-y-auto p-6 flex flex-col justify-between">
                <div class="space-y-6">
                    @isset($header)
                        <div>
                            {{ $header }}
                        </div>
                    @endisset

                    {{ $slot ?? '' }}
                    @yield('content')
                </div>

                {{-- 🦶 RODAPÉ (FOOTER) COM SUAS CREDENCIAIS --}}
                <footer class="pt-8 mt-12 border-t border-slate-200/80 dark:border-slate-800/60 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs text-slate-500 dark:text-slate-400 shrink-0">
                    <div class="flex items-center gap-2">
                        <span class="font-bold text-slate-800 dark:text-slate-200">Salut Royale</span>
                        <span>&copy; {{ date('Y') }}</span>
                        <span class="hidden sm:inline">&bull;</span>
                        <span class="hidden sm:inline">Todos os direitos reservados.</span>
                    </div>

                    <div class="flex items-center gap-2">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-slate-100 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700/60 text-slate-600 dark:text-slate-300 font-medium text-[11px]">
                            <span class="w-1.5 h-1.5 rounded-full bg-[#295384] dark:bg-[#B99B6C]"></span>
                            Desenvolvido por <strong class="text-slate-900 dark:text-white font-bold">Jorge Eduardo</strong>
                        </span>
                    </div>
                </footer>

            </div>
        </main>
    </div>

    {{-- Drawer Mobile --}}
    <div x-show="mobileSidebarOpen" x-cloak class="relative z-50 lg:hidden" role="dialog" aria-modal="true">
        <div class="fixed inset-0 bg-slate-950/80 backdrop-blur-sm transition-opacity"></div>
        <div class="fixed inset-0 flex">
            <div class="relative flex w-full max-w-xs flex-1 flex-col bg-white dark:bg-slate-900 p-6 border-r border-slate-200 dark:border-slate-800" @click.away="mobileSidebarOpen = false">
                <div class="flex items-center justify-between mb-6">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-xl bg-[#295384] flex items-center justify-center text-[#B99B6C] font-extrabold text-xs shadow-md shrink-0">
                            SR
                        </div>
                        <span class="text-base font-bold text-slate-900 dark:text-white">Salut Royale</span>
                    </div>
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