<?php

namespace App\Livewire\Insured;

use App\Models\Insured;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\TextEntry;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Detalhes do Segurado')]
#[Layout('layouts.app')]
class View extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public Insured $record;

    public function mount(Insured $record): void
    {
        $this->record = $record;
    }

    public function insuredSchema(Schema $schema): Schema
    {
        return $schema
            ->record($this->record)
            ->schema([
                Section::make('Informações Gerais')
                    ->schema([
                        TextEntry::make('name')->label('Nome / Razão Social'),
                        TextEntry::make('document')->label('CPF / CNPJ')->placeholder('Não informado'),
                        TextEntry::make('email')->label('E-mail')->placeholder('Não informado'),
                        TextEntry::make('phone')->label('Telefone')->placeholder('Não informado'),
                        TextEntry::make('person_type')->label('Tipo de Pessoa'),
                        TextEntry::make('assignedTo.name')->label('Consultor Responsável')->placeholder('Não atribuído'),
                    ])->columns(2),

                Section::make('Endereço Registrado')
                    ->schema([
                        TextEntry::make('zip_code')->label('CEP')->placeholder('-'),
                        TextEntry::make('address')->label('Logradouro')->placeholder('-'),
                        TextEntry::make('number')->label('Número')->placeholder('-'),
                        TextEntry::make('complement')->label('Complemento')->placeholder('-'),
                        TextEntry::make('neighborhood')->label('Bairro')->placeholder('-'),
                        TextEntry::make('city')->label('Cidade')->placeholder('-'),
                        TextEntry::make('state')->label('UF')->placeholder('-'),
                    ])->columns(3),

                Section::make('Anotações')
                    ->schema([
                        TextEntry::make('notes')->label('Observações Internas')->placeholder('Nenhuma observação cadastrada.'),
                    ]),
            ]);
    }

    public function render()
    {
        return view('livewire.insured.view');
    }
}