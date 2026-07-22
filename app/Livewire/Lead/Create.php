<?php

namespace App\Livewire\Lead;

use App\Models\Lead;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class Create extends Component implements HasActions, HasSchemas
{
    use InteractsWithActions;
    use InteractsWithSchemas;

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'source' => 'manual',
            'status' => 'novo',
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components(BaseForm::getFields())
            ->statePath('data')
            ->model(Lead::class);
    }

    public function save(): void
    {
        $data = $this->form->getState();

        $record = BaseForm::create($data);

        $this->redirectRoute('leads.index');
    }

    public function render(): View
    {
        return view('livewire.lead.create');
    }
}