<?php

namespace App\Livewire\Claim;

use App\Models\Claim;
use Filament\Actions\ActionGroup;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Table;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

final class ListAll extends Component implements HasActions, HasForms, HasTable
{
    use InteractsWithTable;
    use InteractsWithActions;
    use InteractsWithForms;

    public function table(Table $table): Table
    {
        return $table
            ->query(Claim::query()->where('tenant_id', auth()->user()?->tenant_id ?? 1))
            ->columns([
                TextColumn::make('claim_number')
                    ->label('Nº Sinistro')
                    ->placeholder('S/N')
                    ->searchable(),

                TextColumn::make('insured.name')
                    ->label('Segurado')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('policy.policy_number')
                    ->label('Apólice')
                    ->searchable(),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge(),

                TextColumn::make('occurrence_date')
                    ->label('Data Evento')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),

                TextColumn::make('estimated_amount')
                    ->label('Est. Prejuízo')
                    ->money('BRL')
                    ->sortable(),
            ])
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make('view')
                        ->label('Visualizar')
                        ->icon('heroicon-o-eye')
                        ->color('primary')
                        ->url(fn(Claim $record): string => route('claims.view', $record)),

                    EditAction::make('edit')
                        ->label('Editar')
                        ->url(fn(Claim $record): string => route('claims.edit', $record))
                        ->icon('heroicon-o-pencil')
                        ->color('secondary'),

                    DeleteAction::make('delete')
                        ->label('Excluir')
                        ->icon('heroicon-o-trash')
                        ->color('danger')
                        ->successNotificationTitle('Segurado excluído com sucesso!'),
                ])
                    ->label('Ações'),
            ]);
    }

    public function render()
    {
        return view('livewire.claim.list-all');
    }
}