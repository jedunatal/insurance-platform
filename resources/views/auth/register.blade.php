<!DOCTYPE html>
<html class="h-full bg-slate-100 dark:bg-[#080c14] text-slate-900 dark:text-slate-100 antialiased selection:bg-[#295384] selection:text-white" lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Criar Conta de Corretor | Salut Royale</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        [x-cloak] { display: none !important; }
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>

<body class="min-h-full flex items-center justify-center p-4 sm:p-6 py-12 bg-gradient-to-br from-slate-100 via-slate-50 to-slate-200 dark:from-[#080c14] dark:via-[#0f172a] dark:to-[#020617]">
    <div class="w-full max-w-xl">
        {{-- Card de Cadastro --}}
        <div class="bg-white/80 dark:bg-slate-900/80 backdrop-blur-xl border border-slate-200/80 dark:border-slate-800 rounded-3xl p-8 shadow-2xl transition-all">
            {{-- Logotipo --}}
            <div class="text-center mb-8">
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-gradient-to-br from-[#295384] to-[#1a365d] text-white shadow-lg shadow-[#295384]/30 mb-4 ring-4 ring-[#295384]/10">
                    <svg class="w-8 h-8 text-[#B99B6C]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                </div>
                <h1 class="text-2xl font-extrabold text-slate-900 dark:text-white tracking-tight">Cadastro de Corretor / Corretora</h1>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 font-medium">Inicie a gestão da sua carteira de seguros na nuvem</p>
            </div>

            {{-- Mensagens de Erro --}}
            @if ($errors->any())
                <div class="mb-6 p-4 rounded-xl bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800/60 text-xs text-rose-700 dark:text-rose-300">
                    <div class="flex items-center gap-2 font-bold mb-1">
                        <svg class="w-4 h-4 text-rose-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        Atenção aos seguintes campos:
                    </div>
                    <ul class="list-disc list-inside space-y-0.5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Formulário de Cadastro --}}
            <form action="{{ route('register') }}" method="POST" class="space-y-5">
                @csrf

                <div class="p-4 rounded-2xl bg-slate-50/70 dark:bg-slate-800/40 border border-slate-200/60 dark:border-slate-800/80 space-y-4">
                    <div class="text-xs font-bold uppercase tracking-wider text-[#295384] dark:text-blue-400 flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-[#B99B6C]" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" /></svg>
                        1. Dados da Corretora
                    </div>

                    <div>
                        <label for="brokerage_name" class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1">
                            Nome da Corretora / Razão Social *
                        </label>
                        <input
                            type="text"
                            id="brokerage_name"
                            name="brokerage_name"
                            value="{{ old('brokerage_name') }}"
                            required
                            placeholder="Ex: Prime Seguros & Benefícios"
                            class="w-full px-4 py-2.5 rounded-xl text-sm bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-white placeholder-slate-400 focus:ring-2 focus:ring-[#295384] focus:outline-none transition"
                        >
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label for="document" class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1">
                                CNPJ / CPF do Corretor
                            </label>
                            <input
                                type="text"
                                id="document"
                                name="document"
                                value="{{ old('document') }}"
                                placeholder="00.000.000/0000-00"
                                class="w-full px-4 py-2.5 rounded-xl text-sm bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-white placeholder-slate-400 focus:ring-2 focus:ring-[#295384] focus:outline-none transition"
                            >
                        </div>

                        <div>
                            <label for="phone" class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1">
                                Telefone / WhatsApp
                            </label>
                            <input
                                type="text"
                                id="phone"
                                name="phone"
                                value="{{ old('phone') }}"
                                placeholder="(11) 99999-9999"
                                class="w-full px-4 py-2.5 rounded-xl text-sm bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-white placeholder-slate-400 focus:ring-2 focus:ring-[#295384] focus:outline-none transition"
                            >
                        </div>
                    </div>
                </div>

                <div class="p-4 rounded-2xl bg-slate-50/70 dark:bg-slate-800/40 border border-slate-200/60 dark:border-slate-800/80 space-y-4">
                    <div class="text-xs font-bold uppercase tracking-wider text-[#295384] dark:text-blue-400 flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-[#B99B6C]" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                        2. Dados do Usuário Titular
                    </div>

                    <div>
                        <label for="name" class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1">
                            Nome Completo do Corretor *
                        </label>
                        <input
                            type="text"
                            id="name"
                            name="name"
                            value="{{ old('name') }}"
                            required
                            placeholder="Ex: Carlos Eduardo de Oliveira"
                            class="w-full px-4 py-2.5 rounded-xl text-sm bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-white placeholder-slate-400 focus:ring-2 focus:ring-[#295384] focus:outline-none transition"
                        >
                    </div>

                    <div>
                        <label for="email" class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1">
                            E-mail Corporativo de Acesso *
                        </label>
                        <input
                            type="email"
                            id="email"
                            name="email"
                            value="{{ old('email') }}"
                            required
                            placeholder="corretor@minhacorretora.com"
                            class="w-full px-4 py-2.5 rounded-xl text-sm bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-white placeholder-slate-400 focus:ring-2 focus:ring-[#295384] focus:outline-none transition"
                        >
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label for="password" class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1">
                                Senha *
                            </label>
                            <input
                                type="password"
                                id="password"
                                name="password"
                                required
                                placeholder="••••••••"
                                class="w-full px-4 py-2.5 rounded-xl text-sm bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-white placeholder-slate-400 focus:ring-2 focus:ring-[#295384] focus:outline-none transition"
                            >
                        </div>

                        <div>
                            <label for="password_confirmation" class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1">
                                Confirmar Senha *
                            </label>
                            <input
                                type="password"
                                id="password_confirmation"
                                name="password_confirmation"
                                required
                                placeholder="••••••••"
                                class="w-full px-4 py-2.5 rounded-xl text-sm bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-white placeholder-slate-400 focus:ring-2 focus:ring-[#295384] focus:outline-none transition"
                            >
                        </div>
                    </div>
                </div>

                <button
                    type="submit"
                    class="w-full py-3.5 px-4 rounded-xl text-sm font-bold text-white bg-gradient-to-r from-[#295384] to-[#1f3f66] hover:from-[#1f3f66] hover:to-[#172f4d] shadow-lg shadow-[#295384]/25 hover:shadow-xl transition-all flex items-center justify-center gap-2"
                >
                    <svg class="w-4 h-4 text-[#B99B6C]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Cadastrar e Criar Corretora
                </button>
            </form>

            <div class="text-center mt-6 pt-4 border-t border-slate-100 dark:border-slate-800">
                <span class="text-xs text-slate-500 dark:text-slate-400">Já possui uma conta?</span>
                <a href="{{ route('login') }}" class="text-xs font-bold text-[#295384] dark:text-blue-400 hover:underline ml-1">
                    Fazer Login
                </a>
            </div>
        </div>
    </div>
</body>
</html>
