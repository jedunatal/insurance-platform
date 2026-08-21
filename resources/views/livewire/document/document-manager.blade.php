<div class="space-y-4">
    {{-- Cabeçalho da Seção de Documentos --}}
    <div class="flex items-center justify-between pb-3 border-b border-slate-200 dark:border-slate-800">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-[#295384]/10 dark:bg-[#295384]/20 flex items-center justify-center text-[#295384] dark:text-blue-400">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                </svg>
            </div>
            <div>
                <div class="flex items-center gap-2">
                    <h3 class="text-sm font-bold text-slate-900 dark:text-white uppercase tracking-wider">
                        Documentos & Anexos Privados
                    </h3>
                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-100 dark:bg-emerald-950/60 text-emerald-800 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800/40">
                        🔒 Protegido LGPD
                    </span>
                </div>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                    Arquivos armazenados em disco privado isolado com acesso restrito e autenticado.
                </p>
            </div>
        </div>

        <button
            type="button"
            wire:click="$set('uploadModalOpen', true)"
            class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-bold rounded-xl bg-[#295384] hover:bg-[#1f3f66] text-white shadow-sm transition-all"
        >
            <svg class="w-4 h-4 text-[#B99B6C]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
            </svg>
            Anexar Documento
        </button>
    </div>

    {{-- Lista de Documentos Anexados --}}
    @if ($documents->isEmpty())
        <div class="p-8 text-center border-2 border-dashed border-slate-200 dark:border-slate-800 rounded-2xl bg-slate-50/50 dark:bg-slate-900/20">
            <svg class="w-10 h-10 mx-auto text-slate-400 dark:text-slate-600 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            <p class="text-xs font-medium text-slate-600 dark:text-slate-400">Nenhum documento anexado até o momento.</p>
            <p class="text-[11px] text-slate-400 dark:text-slate-500 mt-1">Anexe CNH, RG, Comprovantes, Fotos ou Propostas assinadas.</p>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
            @foreach ($documents as $doc)
                <div class="flex items-center justify-between p-3.5 rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900/60 shadow-sm hover:border-[#295384]/40 transition-all">
                    <div class="flex items-center gap-3 min-w-0">
                        {{-- Ícone do Arquivo --}}
                        <div class="w-9 h-9 shrink-0 rounded-lg flex items-center justify-center {{ $doc->isPdf() ? 'bg-red-50 dark:bg-red-950/40 text-red-600' : 'bg-blue-50 dark:bg-blue-950/40 text-blue-600' }}">
                            @if ($doc->isPdf())
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" /></svg>
                            @else
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                            @endif
                        </div>

                        {{-- Detalhes --}}
                        <div class="min-w-0">
                            <div class="flex items-center gap-2">
                                <h4 class="text-xs font-bold text-slate-900 dark:text-white truncate">
                                    {{ $doc->title }}
                                </h4>
                                <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-semibold {{ $doc->category?->badgeClasses() ?? 'bg-slate-100 text-slate-700' }}">
                                    {{ $doc->category?->getLabel() ?? 'Outro' }}
                                </span>
                            </div>
                            <div class="flex items-center gap-2 mt-0.5 text-[11px] text-slate-500 dark:text-slate-400">
                                <span class="truncate max-w-[140px]">{{ $doc->original_name }}</span>
                                <span>•</span>
                                <span>{{ $doc->formattedSize() }}</span>
                                <span>•</span>
                                <span>{{ $doc->created_at->format('d/m/Y') }}</span>
                            </div>
                        </div>
                    </div>

                    {{-- Ações Rápidas --}}
                    <div class="flex items-center gap-1 shrink-0 ml-2">
                        {{-- Preview Inline --}}
                        <a
                            href="{{ $doc->previewUrl() }}"
                            target="_blank"
                            title="Visualizar no navegador"
                            class="p-1.5 text-slate-500 hover:text-[#295384] dark:hover:text-blue-400 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-lg transition"
                        >
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                        </a>

                        {{-- Download --}}
                        <a
                            href="{{ $doc->downloadUrl() }}"
                            title="Baixar arquivo seguro"
                            class="p-1.5 text-slate-500 hover:text-emerald-600 dark:hover:text-emerald-400 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-lg transition"
                        >
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                            </svg>
                        </a>

                        {{-- Excluir --}}
                        <button
                            type="button"
                            wire:click="deleteDocument({{ $doc->id }})"
                            wire:confirm="Deseja realmente excluir este documento sensível de forma permanente?"
                            title="Excluir documento"
                            class="p-1.5 text-slate-400 hover:text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-950/40 rounded-lg transition"
                        >
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                        </button>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    {{-- Modal de Upload de Documento --}}
    @if ($uploadModalOpen)
        <div class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl max-w-lg w-full p-6 shadow-2xl space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-lg bg-[#295384]/10 text-[#295384] dark:text-blue-400 flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" /></svg>
                        </div>
                        <h3 class="text-sm font-bold text-slate-900 dark:text-white">Anexar Documento Seguro</h3>
                    </div>
                    <button type="button" wire:click="$set('uploadModalOpen', false)" class="text-slate-400 hover:text-slate-600">✕</button>
                </div>

                <form wire:submit.prevent="uploadDocument" class="space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">
                            Categoria do Documento *
                        </label>
                        <select wire:model="category" class="w-full text-xs rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 p-2.5 text-slate-900 dark:text-white focus:ring-[#295384]">
                            @foreach ($categories as $val => $lbl)
                                <option value="{{ $val }}">{{ $lbl }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">
                            Título / Descrição Curta
                        </label>
                        <input type="text" wire:model="title" placeholder="Ex: CNH Frente e Verso do Titular" class="w-full text-xs rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 p-2.5 text-slate-900 dark:text-white focus:ring-[#295384]">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">
                            Arquivo (PDF, JPG, PNG até 10MB) *
                        </label>
                        <input type="file" wire:model="file" accept=".pdf,.jpg,.jpeg,.png,.webp" class="w-full text-xs rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 p-2 text-slate-900 dark:text-white file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-[#295384] file:text-white hover:file:bg-[#1f3f66]">
                        @error('file') <span class="text-[11px] text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                        <div wire:loading wire:target="file" class="text-[11px] text-[#295384] dark:text-blue-400 mt-1 font-medium">
                            Enviando arquivo para o buffer seguro...
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">
                            Observações Internas (Opcional)
                        </label>
                        <textarea wire:model="notes" rows="2" placeholder="Informações relevantes sobre este documento..." class="w-full text-xs rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 p-2.5 text-slate-900 dark:text-white focus:ring-[#295384]"></textarea>
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100 dark:border-slate-800">
                        <button type="button" wire:click="$set('uploadModalOpen', false)" class="px-4 py-2 text-xs font-medium text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-xl transition">
                            Cancelar
                        </button>
                        <button type="submit" wire:loading.attr="disabled" class="px-4 py-2 text-xs font-bold text-white bg-[#295384] hover:bg-[#1f3f66] rounded-xl shadow-sm transition">
                            Salvar no Armazenamento Seguro
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
