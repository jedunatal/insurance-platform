<?php

namespace App\Livewire\Insured;

use App\Models\Insured;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Notifications\Notification;
use Filament\Schemas\Schema;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
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
            ->schema(BaseForm::getFields())
            ->statePath('data')
            ->model(Insured::class);
    }

    public function save(): void
    {
        $data = $this->form->getState();
        
        $record = BaseForm::create($data);
        
        $this->redirectRoute('insureds.index');
    }

    public function render()
    {
        return view('livewire.insured.create');
    }
}
