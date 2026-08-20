<?php

namespace App\Livewire\Claim;

use App\Models\Claim;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Detalhes do Sinistro')]
#[Layout('layouts.app')]
class View extends Component implements HasActions, HasSchemas
{
    use InteractsWithActions;
    use InteractsWithSchemas;

    public Claim $record;

    public function mount(Claim $record): void
    {
        $this->record = $record->load(['insured', 'policy']);
    }

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->record($this->record)
            ->schema([
                Section::make('Resumo da Ocorrência')
                    ->schema([
                        TextEntry::make('claim_number')->label('Nº Sinistro Interno')->placeholder('S/N'),
                        TextEntry::make('protocol_number')->label('Protocolo Seguradora')->placeholder('-'),
                        TextEntry::make('claim_type')->label('Tipo de Sinistro')->badge(),
                        TextEntry::make('status')->label('Status')->badge(),
                        TextEntry::make('occurrence_date')->label('Data do Evento')->dateTime('d/m/Y H:i'),
                        TextEntry::make('report_date')->label('Data do Aviso')->dateTime('d/m/Y H:i'),
                        TextEntry::make('location')->label('Local da Ocorrência')->placeholder('Não informado')->columnSpanFull(),
                    ])->columns(3),

                Section::make('Partes Vinculadas')
                    ->schema([
                        TextEntry::make('insured.name')->label('Segurado'),
                        TextEntry::make('policy.policy_number')->label('Apólice Vinculada'),
                    ])->columns(2),

                Section::make('Descrição do Ocorrido')
                    ->schema([
                        TextEntry::make('occurrence_description')->label('Detalhes')->columnSpanFull(),
                    ]),

                Section::make('Financeiro e Franquia')
                    ->schema([
                        TextEntry::make('estimated_amount')->label('Prejuízo Estimado')->money('BRL'),
                        TextEntry::make('deductible_amount')->label('Valor da Franquia')->money('BRL'),
                        TextEntry::make('indemnified_amount')->label('Valor Indenizado')->money('BRL'),
                    ])->columns(3),

                Section::make('Observações')
                    ->schema([
                        TextEntry::make('notes')->label('Notas Internas')->placeholder('Nenhuma observação cadastrada.')->columnSpanFull(),
                    ]),
            ]);
    }

    public function render()
    {
        return view('livewire.claim.view');
    }
}