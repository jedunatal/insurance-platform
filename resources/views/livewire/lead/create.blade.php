<div class="flex flex-col gap-y-6 w-full max-w-7xl mx-auto px-4 sm:px-6 py-2">

    {{-- Cabeçalho da Página --}}
    <x-page-header 
        category="CRM Comercial" 
        title="Novo Cliente em Potencial" 
        description="Cadastre as informações para iniciar o acompanhamento no seu funil de vendas."
    >
        <x-slot:actions>
            <a href="{{ route('leads.index') }}" wire:navigate class="inline-flex items-center justify-center px-4 py-2.5 text-sm font-bold text-gray-600 dark:text-neutral-300 bg-gray-100 dark:bg-neutral-900 hover:bg-gray-200 dark:hover:bg-neutral-800 rounded-lg transition-colors">
                ← Voltar para Lista
            </a>
        </x-slot:actions>
    </x-page-header>

    {{-- Formulário no Card Customizado --}}
    <x-card class="!p-6">
        <form wire:submit="save" class="space-y-6">
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                
                {{-- Nome do Lead --}}
                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-gray-700 dark:text-neutral-300 mb-1.5">
                        Nome do Lead <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="text" 
                        wire:model="data.name" 
                        placeholder="Ex: Carlos Henrique Ramos"
                        class="w-full rounded-lg border-gray-300 dark:border-neutral-800 bg-gray-50 dark:bg-neutral-900 text-gray-900 dark:text-white placeholder:text-gray-400 dark:placeholder:text-neutral-500 focus:border-[#295384] focus:ring-[#295384] text-sm py-2.5 px-3.5 shadow-xs transition-colors"
                        required
                    />
                    @error('data.name') <span class="text-xs text-red-500 mt-1 block font-semibold">{{ $message }}</span> @enderror
                </div>

                {{-- E-mail --}}
                <div>
                    <label class="block text-xs font-bold text-gray-700 dark:text-neutral-300 mb-1.5">
                        E-mail
                    </label>
                    <input 
                        type="email" 
                        wire:model="data.email" 
                        placeholder="carlos@email.com"
                        class="w-full rounded-lg border-gray-300 dark:border-neutral-800 bg-gray-50 dark:bg-neutral-900 text-gray-900 dark:text-white placeholder:text-gray-400 dark:placeholder:text-neutral-500 focus:border-[#295384] focus:ring-[#295384] text-sm py-2.5 px-3.5 shadow-xs transition-colors"
                    />
                    @error('data.email') <span class="text-xs text-red-500 mt-1 block font-semibold">{{ $message }}</span> @enderror
                </div>

                {{-- Telefone / WhatsApp --}}
                <div>
                    <label class="block text-xs font-bold text-gray-700 dark:text-neutral-300 mb-1.5">
                        Telefone / WhatsApp
                    </label>
                    <input 
                        type="text" 
                        wire:model="data.phone" 
                        placeholder="(21) 99999-9999"
                        class="w-full rounded-lg border-gray-300 dark:border-neutral-800 bg-gray-50 dark:bg-neutral-900 text-gray-900 dark:text-white placeholder:text-gray-400 dark:placeholder:text-neutral-500 focus:border-[#295384] focus:ring-[#295384] text-sm py-2.5 px-3.5 shadow-xs transition-colors"
                    />
                </div>

                {{-- CPF / CNPJ --}}
                <div>
                    <label class="block text-xs font-bold text-gray-700 dark:text-neutral-300 mb-1.5">
                        CPF / CNPJ
                    </label>
                    <input 
                        type="text" 
                        wire:model="data.document" 
                        placeholder="Apenas números"
                        class="w-full rounded-lg border-gray-300 dark:border-neutral-800 bg-gray-50 dark:bg-neutral-900 text-gray-900 dark:text-white placeholder:text-gray-400 dark:placeholder:text-neutral-500 focus:border-[#295384] focus:ring-[#295384] text-sm py-2.5 px-3.5 shadow-xs transition-colors"
                    />
                </div>

                {{-- Ramo / Produto de Interesse --}}
                <div>
                    <label class="block text-xs font-bold text-gray-700 dark:text-neutral-300 mb-1.5">
                        Ramo / Produto de Interesse
                    </label>
                    <select 
                        wire:model="data.product_id" 
                        class="w-full rounded-lg border-gray-300 dark:border-neutral-800 bg-gray-50 dark:bg-neutral-900 text-gray-900 dark:text-white focus:border-[#295384] focus:ring-[#295384] text-sm py-2.5 px-3.5 shadow-xs transition-colors"
                    >
                        <option value="">Selecione um produto...</option>
                        @foreach($this->products as $product)
                            <option value="{{ $product->id }}">{{ $product->name }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Origem do Lead --}}
                <div>
                    <label class="block text-xs font-bold text-gray-700 dark:text-neutral-300 mb-1.5">
                        Origem do Lead <span class="text-red-500">*</span>
                    </label>
                    <select 
                        wire:model="data.source" 
                        class="w-full rounded-lg border-gray-300 dark:border-neutral-800 bg-gray-50 dark:bg-neutral-900 text-gray-900 dark:text-white focus:border-[#295384] focus:ring-[#295384] text-sm py-2.5 px-3.5 shadow-xs transition-colors"
                        required
                    >
                        @foreach($this->sources as $source)
                            <option value="{{ $source->value }}">{{ $source->name }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Status Inicial --}}
                <div>
                    <label class="block text-xs font-bold text-gray-700 dark:text-neutral-300 mb-1.5">
                        Status Inicial <span class="text-red-500">*</span>
                    </label>
                    <select 
                        wire:model="data.status" 
                        class="w-full rounded-lg border-gray-300 dark:border-neutral-800 bg-gray-50 dark:bg-neutral-900 text-gray-900 dark:text-white focus:border-[#295384] focus:ring-[#295384] text-sm py-2.5 px-3.5 shadow-xs transition-colors"
                        required
                    >
                        @foreach($this->statuses as $status)
                            <option value="{{ $status->value }}">{{ $status->name }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Agendar Próximo Contato --}}
                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-gray-700 dark:text-neutral-300 mb-1.5">
                        Agendar Próximo Contato
                    </label>
                    <input 
                        type="datetime-local" 
                        wire:model="data.next_contact_at" 
                        class="w-full rounded-lg border-gray-300 dark:border-neutral-800 bg-gray-50 dark:bg-neutral-900 text-gray-900 dark:text-white focus:border-[#295384] focus:ring-[#295384] text-sm py-2.5 px-3.5 shadow-xs transition-colors"
                    />
                </div>

                {{-- Observações --}}
                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-gray-700 dark:text-neutral-300 mb-1.5">
                        Notas / Observações da Negociação
                    </label>
                    <textarea 
                        wire:model="data.notes" 
                        rows="3" 
                        placeholder="Anote aqui detalhes da negociação, histórico de contato ou coberturas..."
                        class="w-full rounded-lg border-gray-300 dark:border-neutral-800 bg-gray-50 dark:bg-neutral-900 text-gray-900 dark:text-white placeholder:text-gray-400 dark:placeholder:text-neutral-500 focus:border-[#295384] focus:ring-[#295384] text-sm py-2.5 px-3.5 shadow-xs transition-colors"
                    ></textarea>
                </div>

            </div>

            {{-- Ações Inferiores --}}
            <div class="pt-6 border-t border-gray-200 dark:border-neutral-800 flex items-center justify-start gap-3">
                <button 
                    type="submit" 
                    class="px-5 py-2.5 text-sm font-bold text-white bg-[#295384] hover:bg-opacity-90 rounded-lg transition-colors shadow-xs"
                >
                    Salvar Cliente
                </button>
                <a 
                    href="{{ route('leads.index') }}" 
                    wire:navigate 
                    class="px-4 py-2.5 text-sm font-semibold text-gray-600 dark:text-neutral-300 hover:text-gray-900 dark:hover:text-white transition-colors"
                >
                    Cancelar
                </a>
            </div>

        </form>
    </x-card>

</div>