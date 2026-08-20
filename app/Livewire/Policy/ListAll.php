<?php

namespace App\Livewire\Policy;

use App\Models\Policy;
use App\Enums\PolicyStatusEnum;
use App\Enums\InsuranceBranchEnum;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\ViewAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Tables\Filters\SelectFilter;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Apólices')]
#[Layout('layouts.app')]
class ListAll extends Component implements HasTable, HasActions, HasForms
{
    use InteractsWithTable;
    use InteractsWithActions;
    use InteractsWithForms;

    public function table(Table $table): Table
    {
        return $table
            ->query(Policy::query()->with(['insured', 'product', 'broker'])->latest())
            ->headerActions([
                CreateAction::make('create')
                    ->label('Nova Apólice')
                    ->icon('heroicon-o-plus')
                    ->url(route('policies.create'))
                    ->extraAttributes([
                        'class' => '!bg-[#295384] hover:!bg-[#1c385a] !text-white [&_svg]:!text-white font-medium transition-colors shadow-xs',
                    ]),
            ])
            ->columns([
                TextColumn::make('policy_number')
                    ->label('Nº da Apólice')
                    ->searchable()
                    ->sortable()
                    ->description(fn(Policy $record): ?string => $record->insurer ?? 'Seguradora não informada'),

                TextColumn::make('insured.name')
                    ->label('Segurado')
                    ->searchable()
                    ->sortable()
                    ->description(fn(Policy $record): ?string => $record->branch ? "Ramo: {$record->branch}" : null),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge(),

                TextColumn::make('total_premium')
                    ->label('Prêmio Total')
                    ->money('BRL')
                    ->sortable()
                    ->description(fn(Policy $record): ?string => $record->deductible_amount > 0 ? "Franquia: " . $record->formattedDeductibleAmount() : null),

                TextColumn::make('end_date')
                    ->label('Vigência')
                    ->formatStateUsing(fn (Policy $record): string => $record->start_date && $record->end_date 
                        ? $record->start_date->format('d/m/Y') . ' até ' . $record->end_date->format('d/m/Y')
                        : ($record->end_date ? $record->end_date->format('d/m/Y') : '-'))
                    ->sortable(),
            ])
            ->recordActions([
                ActionGroup::make([
                    Action::make('document')
                        ->label('Certificado / Imprimir PDF')
                        ->icon('heroicon-o-printer')
                        ->color('info')
                        ->url(fn (Policy $record): string => route('policies.document.view', $record))
                        ->openUrlInNewTab(),

                    Action::make('claim')
                        ->label('Avisar Sinistro')
                        ->icon('heroicon-o-exclamation-triangle')
                        ->color('warning')
                        ->url(fn (Policy $record): string => route('claims.create', ['policy_id' => $record->id])),

                    ViewAction::make('view')
                        ->label('Visualizar')
                        ->icon('heroicon-o-eye')
                        ->color('primary')
                        ->url(fn(Policy $record): string => route('policies.view', $record)),

                    EditAction::make('edit')
                        ->label('Editar')
                        ->icon('heroicon-o-pencil')
                        ->color('secondary')
                        ->url(fn(Policy $record): string => route('policies.edit', $record)),

                    DeleteAction::make('delete')
                        ->label('Excluir')
                        ->icon('heroicon-o-trash')
                        ->color('danger')
                        ->successNotificationTitle('Apólice excluída com sucesso!'),    
                ])
                    ->label('Ações'),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Filtrar por Status')
                    ->options(PolicyStatusEnum::options()),

                SelectFilter::make('branch')
                    ->label('Filtrar por Ramo')
                    ->options(InsuranceBranchEnum::options()),
            ])
            ->emptyStateHeading('Nenhuma apólice encontrada')
            ->emptyStateDescription('Não encontramos registros correspondentes à pesquisa ou filtro selecionado.');
    }

    public function render()
    {
        return view('livewire.policy.list-all');
    }
}