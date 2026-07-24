<?php

namespace App\Livewire\Insured;

use App\Models\Insured;
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

#[Title('Segurados')]
#[Layout('layouts.app')]
final class ListAll extends Component implements HasActions, HasForms, HasTable
{
    use InteractsWithActions;
    use InteractsWithForms;
    use InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table
            ->query(Insured::query()->latest())
            ->headerActions([
                CreateAction::make('create')
                    ->label('Novo Segurado')
                    ->url(route('insureds.create'))
                    ->icon('heroicon-o-plus')
                    ->extraAttributes([
                        'class' => '!bg-[#295384] hover:!bg-[#1c385a] !text-white [&_svg]:!text-white font-medium transition-colors shadow-xs',
                    ]),
            ])
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Segurado')
                    ->searchable()
                    ->sortable()
                    ->description(fn(Insured $record): string => $record->document ? "CPF/CNPJ: {$record->document}" : 'Documento não informado'),

                Tables\Columns\TextColumn::make('email')
                    ->label('Contato')
                    ->searchable()
                    ->description(fn(Insured $record): ?string => $record->phone),

                Tables\Columns\TextColumn::make('city')
                    ->label('Cidade/UF')
                    ->formatStateUsing(fn(Insured $record): string => $record->city ? "{$record->city}/{$record->state}" : 'Não informada')
                    ->searchable(),

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
                        ->url(fn(Insured $record): string => route('insureds.view', $record)),

                    EditAction::make('edit')
                        ->label('Editar')
                        ->url(fn(Insured $record): string => route('insureds.edit', $record))
                        ->icon('heroicon-o-pencil')
                        ->color('secondary'),

                    DeleteAction::make('delete')
                        ->label('Excluir')
                        ->icon('heroicon-o-trash')
                        ->color('danger')
                        ->successNotificationTitle('Segurado excluído com sucesso!'),
                ])
                    ->label('Ações'),
            ])
            ->filters([
                SelectFilter::make('person_type')
                    ->label('Filtrar por Tipo')
                    ->options([
                        'PF' => 'Pessoa Física (CPF)',
                        'PJ' => 'Pessoa Jurídica (CNPJ)',
                    ]),
            ])
            ->emptyStateHeading('Nenhum segurado encontrado')
            ->emptyStateDescription('Não encontramos registros correspondentes à pesquisa ou filtro selecionado.');
    }

    public function render(): \Illuminate\View\View
    {
        return view('livewire.insured.list-all');
    }
}