<?php

namespace App\Livewire\Claim;

use App\DTOs\ClaimData;
use App\Models\Claim;
use App\Services\Insurance\ClaimService;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Schemas\Schema;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Editar Sinistro')]
#[Layout('layouts.app')]
class Edit extends Component implements HasForms, HasActions
{
    use InteractsWithForms;
    use InteractsWithActions;

    public Claim $record;
    public ?array $data = [];

    public function mount(Claim $record): void
    {
        $this->record = $record;
        $this->form->fill($record->toArray());
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema(BaseForm::getFields())
            ->statePath('data');
    }

    public function save(ClaimService $service)
    {
        $formData = $this->form->getState();
        $dto = ClaimData::fromArray(array_merge($this->record->toArray(), $formData));

        $service->update($this->record, $dto);

        session()->flash('success', 'Sinistro atualizado com sucesso!');
        return redirect()->route('claims.index');
    }

    public function render()
    {
        return view('livewire.claim.edit');
    }
}