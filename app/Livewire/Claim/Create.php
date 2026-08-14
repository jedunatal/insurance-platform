<?php

namespace App\Livewire\Claim;

use App\DTOs\ClaimData;
use App\Services\Insurance\ClaimService;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Avisar Sinistro')]
#[Layout('layouts.app')]
class Create extends Component implements HasForms, HasActions
{
    use InteractsWithForms;
    use InteractsWithActions;

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Form $form): Form
    {
        return BaseForm::configure($form)
            ->statePath('data');
    }

    public function save(ClaimService $service)
    {
        $formData = $this->form->getState();
        $formData['tenant_id'] = auth()->user()?->tenant_id ?? 1;
        $formData['created_by'] = auth()->id();

        $dto = ClaimData::fromArray($formData);
        $service->create($dto);

        session()->flash('success', 'Sinistro registrado com sucesso!');
        return redirect()->route('claims.index');
    }

    public function render()
    {
        return view('livewire.claim.create');
    }
}