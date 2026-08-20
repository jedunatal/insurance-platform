<?php

namespace App\Livewire\Claim;

use App\Enums\ClaimStatusEnum;
use App\Enums\ClaimTypeEnum;
use App\Models\Claim;
use Filament\Actions\ActionGroup;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Sinistros')]
#[Layout('layouts.app')]
final class ListAll extends Component implements HasActions, HasForms, HasTable
{
    use InteractsWithTable;
    use InteractsWithActions;
    use InteractsWithForms;

    public function table(Table $table): Table
    {
        return $table
            ->query(Claim::query()->with(['insured', 'policy'])->latest())
            ->headerActions([
                CreateAction::make('create')
                    ->label('Avisar Sinistro')
                    ->icon('heroicon-o-plus')
                    ->url(route('claims.create'))
                    ->extraAttributes([
                        'class' => '!bg-[#295384] hover:!bg-[#1c385a] !text-white [&_svg]:!text-white font-medium transition-colors shadow-xs',
                    ]),
            ])
            ->columns([
                TextColumn::make('claim_number')
                    ->label('Nº Sinistro')
                    ->placeholder('S/N')
                    ->searchable()
                    ->description(fn (Claim $record): ?string => $record->protocol_number ? "Prot: {$record->protocol_number}" : null),

                TextColumn::make('insured.name')
                    ->label('Segurado')
                    ->searchable()
                    ->sortable()
                    ->description(fn (Claim $record): ?string => $record->policy?->policy_number ? "Apólice: {$record->policy->policy_number}" : null),

                TextColumn::make('claim_type')
                    ->label('Tipo de Sinistro')
                    ->badge()
                    ->sortable(),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->sortable(),

                TextColumn::make('occurrence_date')
                    ->label('Data Evento')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),

                TextColumn::make('estimated_amount')
                    ->label('Est. Prejuízo')
                    ->money('BRL')
                    ->sortable(),

                TextColumn::make('indemnified_amount')
                    ->label('Indenizado')
                    ->money('BRL')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
                        ->successNotificationTitle('Sinistro excluído com sucesso!'),
                ])
                    ->label('Ações'),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Status')
                    ->options(ClaimStatusEnum::options()),

                SelectFilter::make('claim_type')
                    ->label('Tipo de Sinistro')
                    ->options(ClaimTypeEnum::options()),
            ])
            ->emptyStateHeading('Nenhum sinistro encontrado')
            ->emptyStateDescription('Não encontramos ocorrências correspondentes aos filtros selecionados.');
    }

    public function render()
    {
        return view('livewire.claim.list-all');
    }
}