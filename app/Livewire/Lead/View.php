<?php

namespace App\Livewire\Lead;

use App\Models\Lead;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Concerns\InteractsWithInfolists;
use Filament\Infolists\Contracts\HasInfolists;
use Filament\Infolists\Infolist;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class View extends Component implements HasActions, HasInfolists
{
    use InteractsWithActions;
    use InteractsWithInfolists;

    // O registro do Lead injetado pela rota
    public Lead $record;

    /**
     * Inicializa o componente injetando a model automaticamente (Route Model Binding)
     */
    public function mount(Lead $record): void
    {
        $this->record = $record;
    }

    /**
     * Define a exibição dos dados em modo de leitura (Infolist padrão SGI2)
     */
    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->record($this->record)
            ->schema([
                // Estrutura de exibição usando o grid nativo do Filament
                TextEntry::make('name')
                    ->label('Nome do Lead'),

                TextEntry::make('email')
                    ->label('E-mail'),

                TextEntry::make('phone')
                    ->label('Telefone')
                    ->placeholder('Não informado'),

                TextEntry::make('status.name')
                    ->label('Status Atual')
                    ->badge(),

                TextEntry::make('created_at')
                    ->label('Cadastrado em')
                    ->dateTime('d/m/Y H:i'),
            ])->columns(2); // Divide a exibição em duas colunas organizadas
    }

    public function render(): View
    {
        return view('livewire.lead.view');
    }
}