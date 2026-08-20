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
use Livewire\Attributes\Url;
use Livewire\Component;

#[Title('Avisar Sinistro')]
#[Layout('layouts.app')]
class Create extends Component implements HasForms, HasActions
{
    use InteractsWithForms;
    use InteractsWithActions;

    #[Url]
    public ?int $policy_id = null;

    #[Url]
    public ?int $insured_id = null;

    public ?array $data = [];

    public function mount(): void
    {
        $initialData = [
            'status' => 'reported',
            'report_date' => now()->format('Y-m-d H:i:s'),
        ];

        if ($this->policy_id) {
            $initialData['policy_id'] = $this->policy_id;
            $policy = \App\Models\Policy::find($this->policy_id);
            if ($policy) {
                $initialData['insured_id'] = $policy->insured_id;
                if ($policy->deductible_amount > 0) {
                    $initialData['deductible_amount'] = (float) $policy->deductible_amount;
                }
            }
        } elseif ($this->insured_id) {
            $initialData['insured_id'] = $this->insured_id;
        }

        $this->form->fill($initialData);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components(BaseForm::getFields())
            ->statePath('data')
            ->model(Claim::class);
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