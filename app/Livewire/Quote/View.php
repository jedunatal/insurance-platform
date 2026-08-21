<?php

namespace App\Livewire\Quote;

use App\Actions\Quote\ConvertQuoteToPolicyAction;
use App\Enums\QuoteStatusEnum;
use App\Models\Quote;
use App\Models\QuoteOption;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Notifications\Notification;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Comparativo de Cotação')]
#[Layout('layouts.app')]
class View extends Component implements HasActions
{
    use InteractsWithActions;

    public Quote $record;

    public function mount(Quote $record): void
    {
        $this->record = $record->loadMissing(['options', 'lead', 'insured', 'convertedPolicy']);
    }

    public function acceptOption(int $optionId)
    {
        $option = QuoteOption::findOrFail($optionId);
        $policy = app(ConvertQuoteToPolicyAction::class)->execute($this->record, $option);

        Notification::make()
            ->title('Proposta Aceita & Apólice Emitida!')
            ->body("A cotação foi convertida na apólice #{$policy->policy_number}.")
            ->success()
            ->send();

        return redirect()->route('policies.view', $policy);
    }

    public function render()
    {
        return view('livewire.quote.view');
    }
}
