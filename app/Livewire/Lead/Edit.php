<?php

namespace App\Livewire\Lead;

use App\Models\Lead;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Notifications\Notification;
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

    public ?array $data = [];

    public function mount(Lead $record): void
    {
        $this->record = $record;
        $this->record->load(['product', 'assignedTo']);

        $this->form->fill([
            ...$this->record->attributesToArray(),
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                ...BaseForm::getFields()
            ])
            ->statePath('data')
            ->model($this->record);
    }

    public function save(): void
    {
        $data = $this->form->getState();

        DB::transaction(function () use ($data) {
            $this->record->update($data);

            Notification::make()
                ->title('Cliente em potencial atualizado com sucesso!')
                ->success()
                ->send();

            $this->redirect(route('leads.index'));
        });
    }

    public function render(): View
    {
        return view('livewire.lead.edit');
    }
}