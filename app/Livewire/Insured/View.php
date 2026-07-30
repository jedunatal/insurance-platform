<?php

namespace App\Livewire\Insured;

use App\Models\Insured;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Schemas\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Detalhes do Segurado')]
#[Layout('layouts.app')]
class View extends Component implements HasActions, HasSchemas
{
    use InteractsWithActions;
    use InteractsWithSchemas;

    public Insured $record;

    public function mount(Insured $record): void
    {
        $record->load('assignedTo');

        $this->record = $record;
    }

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->record($this->record)
            ->schema([
                Section::make('Informações Gerais')
                    ->schema([
                        TextEntry::make('person_type')
                            ->label('Tipo de Pessoa')
                            ->formatStateUsing(fn ($state) => match ($state) {
                                'PF' => 'Pessoa Física',
                                'PJ' => 'Pessoa Jurídica',
                                default => $state ?? 'Não informado',
                            }),

                        TextEntry::make('document')
                            ->label('CPF / CNPJ')
                            ->placeholder('Não informado'),

                        TextEntry::make('name')
                            ->label('Nome / Razão Social')
                            ->columnSpanFull(),

                        TextEntry::make('email')
                            ->label('E-mail')
                            ->placeholder('Não informado'),

                        TextEntry::make('phone')
                            ->label('Telefone / WhatsApp')
                            ->placeholder('Não informado'),

                        TextEntry::make('assignedTo.name')
                            ->label('Consultor Responsável')
                            ->placeholder('Não atribuído')
                            ->columnSpanFull(),
                    ])->columns(2),

                Section::make('Endereço Registrado')
                    ->schema([
                        TextEntry::make('zip_code')
                            ->label('CEP')
                            ->placeholder('-'),

                        TextEntry::make('address')
                            ->label('Endereço')
                            ->placeholder('-')
                            ->columnSpanFull(),

                        TextEntry::make('number')
                            ->label('Número')
                            ->placeholder('-'),

                        TextEntry::make('complement')
                            ->label('Complemento')
                            ->placeholder('-'),

                        TextEntry::make('neighborhood')
                            ->label('Bairro')
                            ->placeholder('-'),

                        TextEntry::make('city')
                            ->label('Cidade')
                            ->placeholder('-'),

                        TextEntry::make('state')
                            ->label('UF')
                            ->placeholder('-'),
                    ])->columns(2),

                Section::make('Anotações')
                    ->schema([
                        TextEntry::make('notes')
                            ->label('Observações')
                            ->placeholder('Nenhuma observação cadastrada.')
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public function render()
    {
        return view('livewire.insured.view');
    }
}