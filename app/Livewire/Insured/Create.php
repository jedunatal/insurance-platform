<?php

namespace App\Livewire\Insured;

use App\Models\Insured;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Novo Segurado')]
#[Layout('layouts.app')]
class Create extends Component implements HasForms
{
    use InteractsWithForms;

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema(BaseForm::schema())
            ->statePath('data');
    }

    public function create(): void
    {
        $data = $this->form->getState();
        $data['tenant_id'] = auth()->user()->tenant_id ?? null;
        $data['created_by'] = auth()->id();

        Insured::create($data);

        \Filament\Notifications\Notification::make()
            ->title('Segurado cadastrado com sucesso!')
            ->success()
            ->send();

        $this->redirect(route('insureds.index'), navigate: true);
    }

    public function render()
    {
        return view('livewire.insured.create');
    }
}