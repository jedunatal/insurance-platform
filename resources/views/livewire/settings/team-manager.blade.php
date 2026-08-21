<div class="space-y-6 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    {{-- Cabeçalho da Página --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-6 border-b border-slate-200 dark:border-slate-800">
        <div>
            <div class="flex items-center gap-2">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-[#295384]/10 text-[#295384] dark:text-blue-400">
                    🏢 {{ auth()->user()->tenant?->name ?? 'Minha Corretora' }}
                </span>
                <span class="text-xs text-slate-400 dark:text-slate-500">•</span>
                <span class="text-xs text-slate-500 dark:text-slate-400 font-medium">Controle de Acesso RBAC</span>
            </div>
            <h1 class="text-2xl font-extrabold text-slate-900 dark:text-white tracking-tight mt-1">
                Gestão da Equipe & Permissões
            </h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                Gerencie corretores parceiros, consultores de vendas e operadores da sua corretora.
            </p>
        </div>

        <button
            type="button"
            wire:click="openCreateModal"
            class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold text-white bg-[#295384] hover:bg-[#1f3f66] shadow-sm hover:shadow transition-all cursor-pointer"
        >
            <svg class="w-4 h-4 text-[#B99B6C]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
            </svg>
            Adicionar Novo Membro
        </button>
    </div>

    {{-- Cards de Resumo da Equipe --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="p-4 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-xs">
            <div class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Total de Membros</div>
            <div class="text-2xl font-extrabold text-slate-900 dark:text-white mt-1">{{ $members->count() }}</div>
        </div>

        <div class="p-4 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-xs">
            <div class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Corretores Gestores</div>
            <div class="text-2xl font-extrabold text-[#295384] dark:text-blue-400 mt-1">
                {{ $members->filter(fn ($u) => $u->hasRole(['broker', 'admin', 'super-admin']))->count() }}
            </div>
        </div>

        <div class="p-4 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-xs">
            <div class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Assistentes / Consultores</div>
            <div class="text-2xl font-extrabold text-emerald-600 dark:text-emerald-400 mt-1">
                {{ $members->filter(fn ($u) => $u->hasRole(['assistant', 'consultant']))->count() }}
            </div>
        </div>

        <div class="p-4 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-xs">
            <div class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Contas Ativas</div>
            <div class="text-2xl font-extrabold text-slate-900 dark:text-white mt-1">
                {{ $members->where('is_active', true)->count() }}
            </div>
        </div>
    </div>

    {{-- Tabela de Membros da Equipe --}}
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 dark:bg-slate-800/60 border-b border-slate-200 dark:border-slate-800 text-slate-500 dark:text-slate-400 uppercase tracking-wider font-bold">
                    <tr>
                        <th class="px-6 py-4">Membro / Usuário</th>
                        <th class="px-6 py-4">Cargo / Perfil RBAC</th>
                        <th class="px-6 py-4">Status da Conta</th>
                        <th class="px-6 py-4">Data de Cadastro</th>
                        <th class="px-6 py-4 text-right">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @foreach ($members as $member)
                        <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-800/30 transition-colors">
                            {{-- Nome e Contato --}}
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-[#295384] to-[#1f3f66] text-white font-bold flex items-center justify-center text-xs shrink-0 shadow-xs">
                                        {{ strtoupper(substr($member->name, 0, 2)) }}
                                    </div>
                                    <div>
                                        <div class="font-bold text-slate-900 dark:text-white text-sm">
                                            {{ $member->name }}
                                            @if ($member->id === auth()->id())
                                                <span class="text-[10px] bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 px-1.5 py-0.5 rounded font-semibold ml-1">Você</span>
                                            @endif
                                        </div>
                                        <div class="text-slate-500 dark:text-slate-400 text-xs flex items-center gap-2 mt-0.5">
                                            <span>{{ $member->email }}</span>
                                            @if ($member->phone)
                                                <span>•</span>
                                                <span>{{ $member->phone }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </td>

                            {{-- Perfil RBAC --}}
                            <td class="px-6 py-4">
                                @if ($member->hasRole(['super-admin', 'admin']))
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-bold bg-purple-50 text-purple-700 dark:bg-purple-950/60 dark:text-purple-300 border border-purple-200 dark:border-purple-800/40">
                                        👑 Administrador Geral
                                    </span>
                                @elseif ($member->hasRole('broker'))
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-bold bg-blue-50 text-[#295384] dark:bg-blue-950/60 dark:text-blue-300 border border-blue-200 dark:border-blue-800/40">
                                        💼 Corretor Gestor (Acesso Total)
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-bold bg-emerald-50 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800/40">
                                        📋 Assistente / Consultor
                                    </span>
                                @endif
                            </td>

                            {{-- Status Ativo/Inativo --}}
                            <td class="px-6 py-4">
                                @if ($member->is_active)
                                    <span class="inline-flex items-center gap-1 text-emerald-600 dark:text-emerald-400 font-bold">
                                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                                        Ativo
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 text-slate-400 dark:text-slate-500 font-bold">
                                        <span class="w-2 h-2 rounded-full bg-slate-400"></span>
                                        Inativo
                                    </span>
                                @endif
                            </td>

                            {{-- Data de Criação --}}
                            <td class="px-6 py-4 text-slate-500 dark:text-slate-400">
                                {{ $member->created_at->format('d/m/Y') }}
                            </td>

                            {{-- Ações --}}
                            <td class="px-6 py-4 text-right">
                                <div class="inline-flex items-center gap-1">
                                    {{-- Editar --}}
                                    <button
                                        type="button"
                                        wire:click="openEditModal({{ $member->id }})"
                                        title="Editar permissões e dados"
                                        class="p-1.5 text-slate-500 hover:text-[#295384] dark:hover:text-blue-400 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-lg transition"
                                    >
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </button>

                                    {{-- Ativar / Inativar --}}
                                    @if ($member->id !== auth()->id())
                                        <button
                                            type="button"
                                            wire:click="toggleUserStatus({{ $member->id }})"
                                            title="{{ $member->is_active ? 'Inativar acesso' : 'Ativar acesso' }}"
                                            class="p-1.5 {{ $member->is_active ? 'text-amber-500 hover:bg-amber-50 dark:hover:bg-amber-950/40' : 'text-emerald-500 hover:bg-emerald-50 dark:hover:bg-emerald-950/40' }} rounded-lg transition"
                                        >
                                            @if ($member->is_active)
                                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" /></svg>
                                            @else
                                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                            @endif
                                        </button>

                                        {{-- Excluir --}}
                                        <button
                                            type="button"
                                            wire:click="deleteMember({{ $member->id }})"
                                            wire:confirm="Deseja realmente remover o acesso deste membro da equipe?"
                                            title="Excluir membro"
                                            class="p-1.5 text-slate-400 hover:text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-950/40 rounded-lg transition"
                                        >
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- Modal de Criação / Edição de Membro --}}
    @if ($modalOpen)
        <div class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl max-w-lg w-full p-6 shadow-2xl space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-xl bg-[#295384]/10 text-[#295384] dark:text-blue-400 flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" /></svg>
                        </div>
                        <h3 class="text-sm font-bold text-slate-900 dark:text-white">
                            {{ $editingUserId ? 'Editar Membro da Equipe' : 'Adicionar Novo Membro da Corretora' }}
                        </h3>
                    </div>
                    <button type="button" wire:click="$set('modalOpen', false)" class="text-slate-400 hover:text-slate-600">✕</button>
                </div>

                <form wire:submit.prevent="saveMember" class="space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">
                            Nome Completo *
                        </label>
                        <input type="text" wire:model="name" placeholder="Ex: Roberto Silveira" class="w-full text-xs rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 p-2.5 text-slate-900 dark:text-white focus:ring-[#295384]">
                        @error('name') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">
                            E-mail Corporativo *
                        </label>
                        <input type="email" wire:model="email" placeholder="membro@corretora.com" class="w-full text-xs rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 p-2.5 text-slate-900 dark:text-white focus:ring-[#295384]">
                        @error('email') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">
                            Telefone / WhatsApp
                        </label>
                        <input type="text" wire:model="phone" placeholder="(11) 99999-9999" class="w-full text-xs rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 p-2.5 text-slate-900 dark:text-white focus:ring-[#295384]">
                        @error('phone') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">
                            Perfil de Acesso & Permissões (RBAC) *
                        </label>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mt-1.5">
                            <label class="flex flex-col p-3 rounded-xl border {{ $role === 'broker' ? 'border-[#295384] bg-blue-50/50 dark:bg-blue-950/20' : 'border-slate-200 dark:border-slate-700' }} cursor-pointer">
                                <div class="flex items-center gap-2">
                                    <input type="radio" wire:model.live="role" value="broker" class="text-[#295384]">
                                    <span class="text-xs font-bold text-slate-900 dark:text-white">Corretor Gestor</span>
                                </div>
                                <span class="text-[11px] text-slate-500 dark:text-slate-400 mt-1">Acesso completo operacional, financeiro e equipe.</span>
                            </label>

                            <label class="flex flex-col p-3 rounded-xl border {{ $role === 'assistant' ? 'border-emerald-600 bg-emerald-50/50 dark:bg-emerald-950/20' : 'border-slate-200 dark:border-slate-700' }} cursor-pointer">
                                <div class="flex items-center gap-2">
                                    <input type="radio" wire:model.live="role" value="assistant" class="text-emerald-600">
                                    <span class="text-xs font-bold text-slate-900 dark:text-white">Assistente / Consultor</span>
                                </div>
                                <span class="text-[11px] text-slate-500 dark:text-slate-400 mt-1">Cadastros e cotações (sem exclusão de dados).</span>
                            </label>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">
                            {{ $editingUserId ? 'Nova Senha (Deixe em branco para manter)' : 'Senha de Acesso *' }}
                        </label>
                        <input type="password" wire:model="password" placeholder="••••••••" class="w-full text-xs rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 p-2.5 text-slate-900 dark:text-white focus:ring-[#295384]">
                        @error('password') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100 dark:border-slate-800">
                        <button type="button" wire:click="$set('modalOpen', false)" class="px-4 py-2 text-xs font-medium text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-xl transition">
                            Cancelar
                        </button>
                        <button type="submit" class="px-4 py-2 text-xs font-bold text-white bg-[#295384] hover:bg-[#1f3f66] rounded-xl shadow-sm transition">
                            {{ $editingUserId ? 'Salvar Alterações' : 'Criar Conta' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
