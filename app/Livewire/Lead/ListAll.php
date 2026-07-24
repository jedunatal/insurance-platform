<?php

namespace App\Livewire\Lead;

use App\Enums\LeadStatusEnum;
use App\Models\Lead;
use Filament\Actions\ActionGroup;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Clientes em Potencial')]
#[Layout('layouts.app')]
final class ListAll extends Component implements HasActions, HasForms, HasTable
{
    use InteractsWithActions;
    use InteractsWithForms;
    use InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table
            ->query(Lead::query()->with('product'))
            ->headerActions([
                CreateAction::make('create')
                    ->label('Novo Cliente')
                    ->url(route('leads.create'))
                    ->icon('heroicon-o-plus')
                    ->extraAttributes([
                        'class' => '!bg-[#295384] hover:!bg-[#1c385a] !text-white [&_svg]:!text-white font-medium transition-colors shadow-xs',
                    ]),
            ])
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Cliente')
                    ->searchable()
                    ->sortable()
                    ->description(fn(Lead $record): string => $record->product?->name ?? 'Ramo não informado'),

                Tables\Columns\TextColumn::make('email')
                    ->label('Contato')
                    ->searchable()
                    ->description(fn(Lead $record): ?string => $record->phone),

                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Criado em')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make('view')
                        ->label('Visualizar')
                        ->icon('heroicon-o-eye')
                        ->color('primary')
                        ->url(fn(Lead $record): string => route('leads.view', $record)),
                    EditAction::make('edit')
                        ->label('Editar')
                        ->url(fn(Lead $record): string => route('leads.edit', $record))
                        ->icon('heroicon-o-pencil')
                        ->color('secondary'),

                    DeleteAction::make('delete')
                        ->label('Excluir')
                        ->icon('heroicon-o-trash')
                        ->color('danger')
                        ->successNotificationTitle('Cliente excluído com sucesso!'),
                ])
                    ->label('Ações'),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Filtrar por Status')
                    ->options(
                        collect(LeadStatusEnum::cases())->pluck('name', 'value')->toArray()
                    ),
            ])
            ->emptyStateHeading('Nenhum cliente em potencial encontrado')
            ->emptyStateDescription('Não encontramos registros correspondentes à pesquisa ou filtro selecionado.');
    }

    public function render(): \Illuminate\View\View
    {
        return view('livewire.lead.list-all');
    }
}