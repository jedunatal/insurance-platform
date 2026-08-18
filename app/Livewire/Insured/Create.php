<?php

namespace App\Livewire\Insured;

use App\Models\Insured;
use App\Models\Lead;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Schemas\Schema;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;

#[Title('Novo Segurado')]
#[Layout('layouts.app')]
class Create extends Component implements HasForms, HasActions
{
    use InteractsWithForms;
    use InteractsWithActions;

    #[Url]
    public ?int $lead_id = null;

    public ?array $data = [];

    public function mount(): void
    {
        $initialData = ['person_type' => 'PF'];

        if ($this->lead_id && $lead = Lead::find($this->lead_id)) {
            $initialData = [
                'lead_id' => $lead->id,
                'name' => $lead->name,
                'email' => $lead->email,
                'phone' => $lead->phone,
                'document' => $lead->document,
                'person_type' => strlen(preg_replace('/\D/', '', (string) $lead->document)) > 11 ? 'PJ' : 'PF',
                'notes' => $lead->notes ? "Convertido do Lead #{$lead->id}.\nObservações do Lead:\n{$lead->notes}" : null,
            ];
        }

        $this->form->fill($initialData);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema(BaseForm::getFields())
            ->statePath('data')
            ->model(Insured::class);
    }

    public function save()
    {
        $formData = $this->form->getState();

        BaseForm::create($formData);

        session()->flash('success', 'Segurado cadastrado com sucesso!');
        return redirect()->route('insureds.index');
    }

    public function render()
    {
        return view('livewire.insured.create');
    }
}