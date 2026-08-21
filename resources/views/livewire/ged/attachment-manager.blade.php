<div class="space-y-4">
    
    {{-- Topo do Módulo GED --}}
    <div class="flex items-center justify-between pb-2 border-b border-slate-200 dark:border-slate-800">
        <div class="flex items-center gap-2">
            <svg class="w-5 h-5 text-[#295384] dark:text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" /></svg>
            <h3 class="text-xs font-bold uppercase tracking-wider text-slate-800 dark:text-slate-200">
                Documentos & Anexos (GED)
            </h3>
        </div>

        <button 
            type="button" 
            wire:click="$set('uploadModalOpen', true)"
            class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-[#295384] hover:bg-[#1f3f66] text-white text-xs font-semibold rounded-xl shadow-xs transition"
        >
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
            Anexar Arquivo
        </button>
    </div>

    {{-- Lista de Arquivos Anexados --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3">
        @forelse($attachments as $att)
            <div class="bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl p-3 flex flex-col justify-between space-y-2 hover:border-[#295384]/40 transition">
                <div>
                    <div class="flex items-start justify-between gap-1">
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold {{ match($att->category?->value ?? $att->category) { 'cnh' => 'bg-blue-100 text-blue-800', 'crlv' => 'bg-indigo-100 text-indigo-800', 'policy' => 'bg-emerald-100 text-emerald-800', 'bo' => 'bg-rose-100 text-rose-800', 'inspection' => 'bg-amber-100 text-amber-800', default => 'bg-slate-200 text-slate-700' } }}">
                            {{ $att->category instanceof \App\Enums\AttachmentCategoryEnum ? $att->category->getLabel() : ucfirst((string)$att->category) }}
                        </span>
                        <span class="text-[10px] text-slate-400">{{ $att->formattedFileSize() }}</span>
                    </div>

                    <h4 class="text-xs font-bold text-slate-900 dark:text-white mt-1.5 line-clamp-1" title="{{ $att->title }}">
                        {{ $att->title }}
                    </h4>
                    <p class="text-[10px] text-slate-500 dark:text-slate-400 line-clamp-1">
                        {{ $att->file_name }}
                    </p>
                </div>

                <div class="pt-2 border-t border-slate-200/60 dark:border-slate-800 flex items-center justify-between">
                    <a 
                        href="{{ $att->url }}" 
                        target="_blank" 
                        class="text-[11px] font-semibold text-[#295384] dark:text-blue-400 hover:underline flex items-center gap-1"
                    >
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                        Visualizar
                    </a>

                    <button 
                        type="button" 
                        wire:click="deleteAttachment({{ $att->id }})"
                        wire:confirm="Tem certeza que deseja remover este anexo?"
                        class="text-[11px] text-rose-600 hover:text-rose-700 font-semibold"
                    >
                        Excluir
                    </button>
                </div>
            </div>
        @empty
            <div class="col-span-full py-6 text-center text-xs text-slate-400 border border-dashed border-slate-200 dark:border-slate-800 rounded-xl">
                Nenhum documento anexado até o momento.
            </div>
        @endforelse
    </div>

    {{-- Modal de Upload --}}
    @if($uploadModalOpen)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs" x-cloak>
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl shadow-2xl max-w-md w-full p-6 space-y-4">
                <div class="flex items-center justify-between">
                    <h3 class="text-sm font-bold text-slate-900 dark:text-white">Anexar Documento</h3>
                    <button type="button" wire:click="$set('uploadModalOpen', false)" class="text-slate-400 hover:text-slate-600">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>

                <form wire:submit.prevent="uploadAttachment" class="space-y-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Título do Documento</label>
                        <input type="text" wire:model="title" placeholder="Ex: CNH do Condutor Principal" class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-700 rounded-xl px-3 py-2 text-xs text-slate-900 dark:text-white focus:outline-none focus:border-[#295384]" required>
                        @error('title') <span class="text-rose-500 text-[10px]">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Categoria</label>
                        <select wire:model="category" class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-700 rounded-xl px-3 py-2 text-xs text-slate-900 dark:text-white focus:outline-none focus:border-[#295384]" required>
                            @foreach($categories as $val => $lbl)
                                <option value="{{ $val }}">{{ $lbl }}</option>
                            @endforeach
                        </select>
                        @error('category') <span class="text-rose-500 text-[10px]">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Arquivo (PDF, Imagem ou Documento)</label>
                        <input type="file" wire:model="file" class="w-full text-xs text-slate-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-[#295384] file:text-white hover:file:bg-[#1f3f66] cursor-pointer" required>
                        <div wire:loading wire:target="file" class="text-[10px] text-[#295384] font-semibold mt-1">Carregando arquivo...</div>
                        @error('file') <span class="text-rose-500 text-[10px]">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Observações (Opcional)</label>
                        <textarea wire:model="notes" rows="2" placeholder="Notas sobre a validade ou versão do documento..." class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-700 rounded-xl px-3 py-2 text-xs text-slate-900 dark:text-white focus:outline-none focus:border-[#295384]"></textarea>
                    </div>

                    <div class="flex justify-end gap-2 pt-3 border-t border-slate-100 dark:border-slate-800">
                        <button type="button" wire:click="$set('uploadModalOpen', false)" class="px-4 py-2 border border-slate-300 dark:border-slate-700 text-slate-700 dark:text-slate-300 text-xs font-medium rounded-xl">Cancelar</button>
                        <button type="submit" wire:loading.attr="disabled" class="px-4 py-2 bg-[#295384] hover:bg-[#1f3f66] text-white text-xs font-semibold rounded-xl">Salvar Documento</button>
                    </div>
                </form>
            </div>
        </div>
    @endif

</div>
