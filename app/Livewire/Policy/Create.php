<?php

namespace App\Livewire\Policy;

use App\Models\Policy;
use App\DTOs\PolicyData;
use App\Services\Insurance\PolicyService;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Schemas\Schema;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Nova Apólice')]
#[Layout('layouts.app')]
class Create extends Component implements HasForms, HasActions
{
    use InteractsWithForms;
    use InteractsWithActions;

    public ?array $data = [];

    public function mount(): void
    {
        /*
        |--------------------------------------------------------------------------
        | TEMPORÁRIO
        |--------------------------------------------------------------------------
        | O projeto ainda não possui autenticação.
        | Quando o login estiver implementado, descomente a linha abaixo.
        |
        | abort_unless(auth()->user()->checkPermissionTo('create policies'), 403);
        |
        */

        $this->form->fill();
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components(BaseForm::getFields())
            ->statePath('data')
            ->model(Policy::class);
    }

    public function save(PolicyService $service)
    {
        $formData = $this->form->getState();

        /*
        |--------------------------------------------------------------------------
        | TEMPORÁRIO
        |--------------------------------------------------------------------------
        | Como ainda não existe usuário autenticado, não é possível definir
        | automaticamente o tenant e o usuário criador da apólice.
        |
        | Código original:
        |
        | $formData['tenant_id'] = auth()->user()->tenant_id;
        | $formData['created_by'] = auth()->id();
        |
        | Quando implementar autenticação, volte para o código acima.
        |--------------------------------------------------------------------------
        */

        $formData['tenant_id'] = null;
        $formData['created_by'] = null;

        $dto = PolicyData::fromArray($formData);

        $service->create($dto);

        session()->flash('success', 'Apólice cadastrada com sucesso!');

        return redirect()->route('policies.index');
    }

    public function render()
    {
        return view('livewire.policy.create');
    }
}