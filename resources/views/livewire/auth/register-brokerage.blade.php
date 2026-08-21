<div class="w-full max-w-xl">
    {{-- Card de Cadastro --}}
    <div class="bg-white/85 dark:bg-slate-900/85 backdrop-blur-xl border border-slate-200/80 dark:border-slate-800 rounded-3xl p-8 shadow-2xl transition-all">
        {{-- Logotipo & Título --}}
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-gradient-to-br from-[#295384] to-[#1a365d] text-white shadow-lg shadow-[#295384]/30 mb-4 ring-4 ring-[#295384]/10">
                <svg class="w-8 h-8 text-[#B99B6C]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                </svg>
            </div>
            <h1 class="text-2xl font-extrabold text-slate-900 dark:text-white tracking-tight">Auto-Cadastro de Corretora</h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 font-medium">Configure seu ambiente seguro multi-tenant na nuvem</p>
        </div>

        {{-- Mensagens de Erro Globais --}}
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

        {{-- Formulário Livewire --}}
        <form wire:submit.prevent="register" class="space-y-5">
            {{-- Seção 1: Dados da Corretora --}}
            <div class="p-4 rounded-2xl bg-slate-50/70 dark:bg-slate-800/40 border border-slate-200/60 dark:border-slate-800/80 space-y-4">
                <div class="text-xs font-bold uppercase tracking-wider text-[#295384] dark:text-blue-400 flex items-center gap-1.5">
                    <svg class="w-4 h-4 text-[#B99B6C]" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" /></svg>
                    1. Dados da Corretora (Tenant)
                </div>

                <div>
                    <label for="brokerage_name" class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1">
                        Razão Social ou Nome Fantasia *
                    </label>
                    <input
                        type="text"
                        id="brokerage_name"
                        wire:model="brokerage_name"
                        placeholder="Ex: Prime Corretora de Seguros"
                        class="w-full px-4 py-2.5 rounded-xl text-sm bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-white placeholder-slate-400 focus:ring-2 focus:ring-[#295384] focus:outline-none transition"
                    >
                    @error('brokerage_name') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label for="cnpj" class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1">
                            CNPJ da Corretora *
                        </label>
                        <input
                            type="text"
                            id="cnpj"
                            wire:model="cnpj"
                            x-mask="99.999.999/9999-99"
                            inputmode="numeric"
                            placeholder="00.000.000/0000-00"
                            class="w-full px-4 py-2.5 rounded-xl text-sm bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-white placeholder-slate-400 focus:ring-2 focus:ring-[#295384] focus:outline-none transition"
                        >
                        @error('cnpj') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label for="brokerage_phone" class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1">
                            Telefone / WhatsApp *
                        </label>
                        <input
                            type="text"
                            id="brokerage_phone"
                            wire:model="brokerage_phone"
                            x-mask:dynamic="$input.replace(/\D/g, '').length > 10 ? '(99) 99999-9999' : '(99) 9999-9999'"
                            inputmode="tel"
                            placeholder="(00) 00000-0000"
                            class="w-full px-4 py-2.5 rounded-xl text-sm bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-white placeholder-slate-400 focus:ring-2 focus:ring-[#295384] focus:outline-none transition"
                        >
                        @error('brokerage_phone') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>

            {{-- Seção 2: Dados do Corretor Administrador --}}
            <div class="p-4 rounded-2xl bg-slate-50/70 dark:bg-slate-800/40 border border-slate-200/60 dark:border-slate-800/80 space-y-4">
                <div class="text-xs font-bold uppercase tracking-wider text-[#295384] dark:text-blue-400 flex items-center gap-1.5">
                    <svg class="w-4 h-4 text-[#B99B6C]" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                    2. Dados do Corretor Titular (Gestor)
                </div>

                <div>
                    <label for="name" class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1">
                        Nome Completo *
                    </label>
                    <input
                        type="text"
                        id="name"
                        wire:model="name"
                        placeholder="Ex: Carlos Eduardo Silveira"
                        class="w-full px-4 py-2.5 rounded-xl text-sm bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-white placeholder-slate-400 focus:ring-2 focus:ring-[#295384] focus:outline-none transition"
                    >
                    @error('name') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label for="email" class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1">
                        E-mail Corporativo de Acesso *
                    </label>
                    <input
                        type="email"
                        id="email"
                        wire:model="email"
                        autocomplete="email"
                        inputmode="email"
                        x-on:input="$event.target.value = $event.target.value.toLowerCase()"
                        placeholder="corretor@minhacorretora.com"
                        class="w-full px-4 py-2.5 rounded-xl text-sm bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-white placeholder-slate-400 focus:ring-2 focus:ring-[#295384] focus:outline-none transition"
                    >
                    @error('email') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label for="password" class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1">
                            Senha de Acesso *
                        </label>
                        <input
                            type="password"
                            id="password"
                            wire:model="password"
                            placeholder="••••••••"
                            class="w-full px-4 py-2.5 rounded-xl text-sm bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-white placeholder-slate-400 focus:ring-2 focus:ring-[#295384] focus:outline-none transition"
                        >
                        @error('password') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label for="password_confirmation" class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1">
                            Confirmar Senha *
                        </label>
                        <input
                            type="password"
                            id="password_confirmation"
                            wire:model="password_confirmation"
                            placeholder="••••••••"
                            class="w-full px-4 py-2.5 rounded-xl text-sm bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-white placeholder-slate-400 focus:ring-2 focus:ring-[#295384] focus:outline-none transition"
                        >
                    </div>
                </div>
            </div>

            <button
                type="submit"
                wire:loading.attr="disabled"
                class="w-full py-3.5 px-4 rounded-xl text-sm font-bold text-white bg-gradient-to-r from-[#295384] to-[#1f3f66] hover:from-[#1f3f66] hover:to-[#172f4d] shadow-lg shadow-[#295384]/25 hover:shadow-xl transition-all flex items-center justify-center gap-2 cursor-pointer"
            >
                <svg wire:loading.remove class="w-4 h-4 text-[#B99B6C]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span wire:loading.remove>Criar Corretora & Iniciar Gestão</span>
                <span wire:loading>Configurando ambiente seguro...</span>
            </button>
        </form>

        <div class="text-center mt-6 pt-4 border-t border-slate-100 dark:border-slate-800">
            <span class="text-xs text-slate-500 dark:text-slate-400">Já possui uma conta ativa?</span>
            <a href="{{ route('login') }}" class="text-xs font-bold text-[#295384] dark:text-blue-400 hover:underline ml-1">
                Fazer Login
            </a>
        </div>
    </div>
</div>
