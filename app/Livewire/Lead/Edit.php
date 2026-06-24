<?php

namespace App\Livewire\Lead;

use App\Models\Lead;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Edit extends Component implements HasActions, HasSchemas
{
    use InteractsWithActions;
    use InteractsWithSchemas;

    public Lead $record;

    // O Filament guarda o estado dos inputs mapeados neste array
    public ?array $data = [];

    /**
     * O Laravel faz o Route Model Binding automático de {record} vindo da rota.
     */
    public function mount(Lead $record): void
    {
        $this->record->load(    "", $record);

        // Preenche o formulário com os dados atuais salvos no banco
        $this->form->fill([
            ...$this->record->attributesToArray(),
        ]);
    }

    /**
     * Define o formulário usando a classe Schema do SGI2.
     */
    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                ...BaseForm::getFields() // Puxa os campos estáticos do seu BaseForm
            ])
            ->statePath('data')
            ->model($this->record);
    }

    /**
     * Processa a validação, persistência e disparo de notificações.
     */
    public function save(): void
    {
        // Valida se o usuário autenticado tem permissão na Policy para atualizar
        $this->authorize('update', $this->record);

        // Valida e extrai os dados tratados pelo Schema
        $data = $this->form->getState();

        // Camada de persistência segura em banco de dados
        DB::transaction(function () use ($data) {
            
            // Atualiza os dados no modelo do Lead
            $this->record->update($data);

            // Dispara a notificação global do SGI2
            notification()
                ->title('Lead atualizado com sucesso!')
                ->success()
                ->send();

            // Redireciona de volta para a listagem principal isolada
            $this->redirect(route('leads.index'));
        });
    }

    public function render(): View
    {
        return view('livewire.lead.edit');
    }
}