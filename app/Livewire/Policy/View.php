<?php

namespace App\Livewire\Policy;

use App\Models\Policy;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\TextEntry;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Detalhes da Apólice')]
#[Layout('layouts.app')]
class View extends Component implements HasActions, HasSchemas
{
    use InteractsWithActions;
    use InteractsWithSchemas;

    public Policy $record;

    public function mount(Policy $record): void
    {
        abort_unless(auth()->user()->checkPermissionTo('view policies') && auth()->user()->tenant_id === $record->tenant_id, 403);
        $this->record = $record->load(['insured', 'product', 'broker']);
    }

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->record($this->record)
            ->schema([
                Section::make('Informações Gerais')
                    ->schema([
                        TextEntry::make('policy_number')->label('Número da Apólice'),
                        TextEntry::make('proposal_number')->label('Proposta')->placeholder('-'),
                        TextEntry::make('branch_code')->label('Ramo')->placeholder('-'),
                        TextEntry::make('status')->label('Status')->badge(),
                        TextEntry::make('start_date')->label('Início da Vigência')->dateTime('d/m/Y'),
                        TextEntry::make('end_date')->label('Fim da Vigência')->dateTime('d/m/Y'),
                    ])->columns(3),

                Section::make('Vínculos e Corretor')
                    ->schema([
                        TextEntry::make('insured.name')->label('Segurado'),
                        TextEntry::make('product.name')->label('Produto')->placeholder('Não informado'),
                        TextEntry::make('broker.name')->label('Corretor Responsável')->placeholder('Não atribuído'),
                    ])->columns(3),

                Section::make('Financeiro')
                    ->schema([
                        TextEntry::make('net_premium')->label('Prêmio Líquido')->money('BRL'),
                        TextEntry::make('iof_amount')->label('IOF')->money('BRL'),
                        TextEntry::make('total_premium')->label('Prêmio Total')->money('BRL'),
                        TextEntry::make('payment_method')->label('Forma de Pagamento'),
                        TextEntry::make('installments_count')->label('Parcelas'),
                    ])->columns(3),

                Section::make('Observações')
                    ->schema([
                        TextEntry::make('notes')->label('Notas')->placeholder('Nenhuma observação.')->columnSpanFull(),
                    ]),
            ]);
    }

    public function render()
    {
        return view('livewire.policy.view');
    }
}