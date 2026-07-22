<?php

namespace App\Livewire\Lead;

use App\Models\Lead;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Concerns\InteractsWithInfolists;
use Filament\Infolists\Contracts\HasInfolists;
use Filament\Infolists\Infolist;
use Illuminate\Contracts\View\View as ViewContract;
use Livewire\Component;

class View extends Component implements HasActions, HasInfolists
{
    use InteractsWithActions;
    use InteractsWithInfolists;

    public Lead $record;

    public function mount(Lead $record): void
    {
        $this->record = $record;
        $this->record->load(['product', 'assignedTo', 'createdBy']);
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->record($this->record)
            ->schema([
                TextEntry::make('name')
                    ->label('Nome do Cliente'),

                TextEntry::make('email')
                    ->label('E-mail')
                    ->placeholder('Não informado'),

                TextEntry::make('phone')
                    ->label('Telefone / WhatsApp')
                    ->placeholder('Não informado'),

                TextEntry::make('document')
                    ->label('CPF / CNPJ')
                    ->placeholder('Não informado'),

                TextEntry::make('product.name')
                    ->label('Ramo / Produto de Interesse')
                    ->placeholder('Não especificado'),

                TextEntry::make('source')
                    ->label('Origem')
                    ->badge(),

                TextEntry::make('status')
                    ->label('Status Atual')
                    ->badge(),

                TextEntry::make('next_contact_at')
                    ->label('Próximo Contato Agendado')
                    ->dateTime('d/m/Y H:i')
                    ->placeholder('Nenhum agendamento'),

                TextEntry::make('assignedTo.name')
                    ->label('Corretor Responsável')
                    ->placeholder('Não atribuído'),

                TextEntry::make('created_at')
                    ->label('Cadastrado em')
                    ->dateTime('d/m/Y H:i'),

                TextEntry::make('notes')
                    ->label('Histórico / Observações')
                    ->columnSpan(2)
                    ->placeholder('Sem observações registradas.'),
            ])->columns(2);
    }

    public function render(): ViewContract
    {
        return view('livewire.lead.view');
    }
}