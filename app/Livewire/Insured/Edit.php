<?php

namespace App\Livewire\Insured;

use App\Models\Insured;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Editar Segurado')]
#[Layout('layouts.app')]
class Edit extends Component implements HasForms
{
    use InteractsWithForms;

    public Insured $record;
    public ?array $data = [];

    public function mount(Insured $record): void
    {
        $this->record = $record;
        $this->form->fill($this->record->toArray());
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema(BaseForm::schema())
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();
        $this->record->update($data);

        \Filament\Notifications\Notification::make()
            ->title('Segurado atualizado com sucesso!')
            ->success()
            ->send();

        $this->redirect(route('insureds.index'), navigate: true);
    }

    public function render()
    {
        return view('livewire.insured.edit');
    }
}