<?php

namespace App\Livewire\Lead;

use App\Models\Lead;
use Livewire\Component;
use Filament\Forms\Contracts\HasForms;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Schemas\Schema;
use Filament\Notifications\Notification;

class Create extends Component implements HasActions, HasForms
{
    use InteractsWithForms;
    use InteractsWithActions;

    // O Filament guarda o estado dos inputs mapeados neste array
    public ?array $data = [];

    public function mount(): void
    {
        // Preenche o formulário inicialmente (vazio ou com defaults)
        $this->form->fill();
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                ...BaseForm::getFields()])
            ->statePath('data')
            ->model(Lead::class);
    }

    public function create(): void
    {
        // Valida e extrai os dados tratados pelo Schema do Filament
        $state = $this->form->getState();
        
        // Exemplo de persistência usando o Model:
        // \App\Models\Lead::create($state);

        Notification::make()
            ->title('Lead cadastrado com sucesso!')
            ->success()
            ->send();

        $this->redirectRoute('leads.index');
    }

    public function render()
    {
        return view('livewire.lead.create');
    }
}