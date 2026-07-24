<?php

namespace App\Livewire\Lead;

use App\Models\Lead;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Illuminate\Contracts\View\View as ViewContract;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Visualizar Cliente')]
#[Layout('layouts.app')]
class View extends Component implements HasActions, HasSchemas
{
    use InteractsWithActions;
    use InteractsWithSchemas;

    public Lead $record;

    public function mount(Lead $record): void
    {
        $record->load('product');

        $this->record = $record;
    }

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->record($this->record)
            ->schema([
                Section::make('Informações do Cliente')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('name')
                                    ->label('Nome do Cliente'),

                                TextEntry::make('email')
                                    ->label('E-mail')
                                    ->placeholder('Sem e-mail'),

                                TextEntry::make('phone')
                                    ->label('Telefone / WhatsApp')
                                    ->placeholder('Sem telefone'),

                                TextEntry::make('document')
                                    ->label('CPF / CNPJ')
                                    ->placeholder('Não informado'),

                                TextEntry::make('product.name')
                                    ->label('Ramo / Produto de Interesse')
                                    ->placeholder('Ramo não informado'),

                                TextEntry::make('source')
                                    ->label('Origem do Cliente'),

                                TextEntry::make('status')
                                    ->label('Status')
                                    ->badge(),

                                TextEntry::make('next_contact_at')
                                    ->label('Próximo Contato')
                                    ->dateTime('d/m/Y H:i')
                                    ->placeholder('Sem agendamento'),

                                TextEntry::make('notes')
                                    ->label('Notas / Observações')
                                    ->columnSpanFull()
                                    ->placeholder('Nenhuma observação cadastrada'),
                            ]),
                    ]),
            ]);
    }

    public function render(): ViewContract
    {
        return view('livewire.lead.view');
    }
}