<?php

namespace App\Livewire\Claim;

use App\Models\Claim;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Sinistros')]
#[Layout('layouts.app')]
class ListAll extends Component implements HasTable, HasActions
{
    use InteractsWithTable;
    use InteractsWithActions;

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
            ->actions([
                \Filament\Tables\Actions\Action::make('view')
                    ->label('Ver')
                    ->url(fn (Claim $record): string => route('claims.view', $record))
                    ->icon('heroicon-m-eye'),

                \Filament\Tables\Actions\Action::make('edit')
                    ->label('Editar')
                    ->url(fn (Claim $record): string => route('claims.edit', $record))
                    ->icon('heroicon-m-pencil-square'),
            ]);
    }

    public function render()
    {
        return view('livewire.claim.list-all');
    }
}