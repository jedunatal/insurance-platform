<?php

namespace App\Livewire\Insured;

use App\Models\Insured;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Schemas\Schema;
use Filament\Notifications\Notification;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

class Edit extends Component implements HasActions, HasSchemas
{
    use InteractsWithActions;
    use InteractsWithSchemas;

    public Insured $record;

    public ?array $data = [];

    public function mount(Insured $record): void
    {
        $this->record = $record;

        $this->form->fill(
            $this->record->only([
                'name',
                'email',
                'phone',
                'document',
                'person_type',
                'zip_code',
                'address',
                'number',
                'complement',
                'neighborhood',
                'city',
                'state',
                'notes',
            ])
        );
    }


    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema(BaseForm::getFields())
            ->statePath('data');
    }


    public function save(): void
    {
        $data = $this->form->getState();
    DB::transaction(function () use ($data) {
            $this->record->update($data);

            Notification::make()
                ->title('Segurado atualizado com sucesso!')
                ->success()
                ->send();

            $this->redirect(
                route('insureds.index'),
                navigate: true
            );
        });
    }


    public function render()
    {
        return view('livewire.insured.edit');
    }
}