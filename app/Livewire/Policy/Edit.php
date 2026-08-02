<?php

namespace App\Livewire\Policy;

use App\DTOs\PolicyData;
use App\Models\Policy;
use App\Services\Insurance\PolicyService;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Schemas\Schema;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Editar Apólice')]
#[Layout('layouts.app')]
class Edit extends Component implements HasForms, HasActions
{
    use InteractsWithForms;
    use InteractsWithActions;

    public Policy $record;
    public ?array $data = [];

    public function mount(Policy $record): void
    {
        /*
        |--------------------------------------------------------------------------
        | TEMPORÁRIO
        |--------------------------------------------------------------------------
        | O projeto ainda não possui autenticação.
        | Quando o login estiver implementado, descomente as linhas abaixo.
        |
        | abort_unless(
        |     auth()->user()->checkPermissionTo('update policies')
        |     && auth()->user()->tenant_id === $record->tenant_id,
        |     403
        | );
        |
        */

        $this->record = $record;
        $this->form->fill($record->toArray());
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components(BaseForm::getFields())
            ->statePath('data')
            ->model($this->record);
    }

    public function save(PolicyService $service)
    {
        $formData = $this->form->getState();

        $dto = PolicyData::fromArray(array_merge($this->record->toArray(), $formData));

        $service->update($this->record, $dto);

        session()->flash('success', 'Apólice atualizada com sucesso!');

        return redirect()->route('policies.index');
    }

    public function render()
    {
        return view('livewire.policy.edit');
    }
}
