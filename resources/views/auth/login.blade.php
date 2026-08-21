<!DOCTYPE html>
<html class="h-full bg-slate-100 dark:bg-[#080c14] text-slate-900 dark:text-slate-100 antialiased selection:bg-[#295384] selection:text-white" lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Acesso Seguro — Insurance Platform</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        [x-cloak] { display: none !important; }
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>

<body class="h-full flex items-center justify-center p-4 sm:p-6 bg-gradient-to-br from-slate-100 via-slate-50 to-slate-200 dark:from-[#080c14] dark:via-[#0f172a] dark:to-[#020617]">
    <div class="w-full max-w-md">
        {{-- Card de Login --}}
        <div class="bg-white/80 dark:bg-slate-900/80 backdrop-blur-xl border border-slate-200/80 dark:border-slate-800 rounded-3xl p-8 shadow-2xl transition-all">
            {{-- Identidade Visual / Logotipo --}}
            <div class="text-center mb-8">
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-gradient-to-br from-[#295384] to-[#1a365d] text-white shadow-lg shadow-[#295384]/30 mb-4 ring-4 ring-[#295384]/10">
                    <svg class="w-8 h-8 text-[#B99B6C]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                </div>
                <h1 class="text-2xl font-extrabold text-slate-900 dark:text-white tracking-tight">Insurance Platform</h1>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 font-medium">Gestão Estratégica & Operacional de Seguros</p>
            </div>

            {{-- Mensagens de Erro --}}
            @if ($errors->any())
                <div class="mb-6 p-4 rounded-xl bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800/60 text-xs text-rose-700 dark:text-rose-300">
                    <div class="flex items-center gap-2 font-bold mb-1">
                        <svg class="w-4 h-4 text-rose-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        Não foi possível autenticar:
                    </div>
                    <ul class="list-disc list-inside space-y-0.5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Formulário de Autenticação --}}
            <form action="{{ route('login') }}" method="POST" class="space-y-5">
                @csrf

                <div>
                    <label for="email" class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">
                        E-mail Corporativo
                    </label>
                    <div class="relative">
                        <input
                            type="email"
                            id="email"
                            name="email"
                            value="{{ old('email') }}"
                            required
                            autofocus
                            placeholder="corretor@corretora.com"
                            class="w-full px-4 py-3 rounded-xl text-sm bg-slate-50 dark:bg-slate-800/80 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-[#295384] focus:border-transparent transition"
                        >
                    </div>
                </div>

                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label for="password" class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300">
                            Senha de Acesso
                        </label>
                    </div>
                    <div class="relative">
                        <input
                            type="password"
                            id="password"
                            name="password"
                            required
                            placeholder="••••••••"
                            class="w-full px-4 py-3 rounded-xl text-sm bg-slate-50 dark:bg-slate-800/80 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-[#295384] focus:border-transparent transition"
                        >
                    </div>
                </div>

                <div class="flex items-center justify-between">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="remember" class="w-4 h-4 rounded text-[#295384] focus:ring-[#295384] border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800">
                        <span class="text-xs text-slate-600 dark:text-slate-400 font-medium">Lembrar neste navegador</span>
                    </label>
                </div>

                <button
                    type="submit"
                    class="w-full py-3.5 px-4 rounded-xl text-sm font-bold text-white bg-gradient-to-r from-[#295384] to-[#1f3f66] hover:from-[#1f3f66] hover:to-[#172f4d] shadow-lg shadow-[#295384]/25 hover:shadow-xl transition-all flex items-center justify-center gap-2"
                >
                    <svg class="w-4 h-4 text-[#B99B6C]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                    </svg>
                    Acessar Plataforma
                </button>
            </form>

            <div class="text-center mt-6 pt-4 border-t border-slate-100 dark:border-slate-800">
                <span class="text-xs text-slate-500 dark:text-slate-400">É um novo corretor?</span>
                <a href="{{ route('register') }}" class="text-xs font-bold text-[#295384] dark:text-blue-400 hover:underline ml-1">
                    Cadastrar Corretora
                </a>
            </div>
        </div>

        <div class="text-center mt-6 text-xs text-slate-400 dark:text-slate-500">
            &copy; {{ date('Y') }} Insurance Platform. Ambiente Seguro com Proteção de Dados LGPD.
        </div>
    </div>
</body>
</html>
