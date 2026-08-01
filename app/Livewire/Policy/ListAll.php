<?php

namespace App\Livewire\Policy;

use App\Models\Policy;
use App\Enums\PolicyStatusEnum;
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

    public function mount(): void
    {
        /*
         |--------------------------------------------------------------------------
         | TEMPORÁRIO
         |--------------------------------------------------------------------------
         | O projeto ainda não possui autenticação.
         | Quando o login estiver implementado, descomente a linha abaixo.
         |
         | abort_unless(auth()->user()->checkPermissionTo('view policies'), 403);
         |
         */
    }

    public function table(Table $table): Table
    {
        return $table
            /*
             |--------------------------------------------------------------------------
             | TEMPORÁRIO
             |--------------------------------------------------------------------------
             | Como ainda não existe usuário autenticado, não é possível filtrar
             | pelo tenant do usuário.
             |
             | Original:
             |
             | ->query(
             |     Policy::query()
             |         ->where('tenant_id', auth()->user()->tenant_id)
             | )
             |
             | Quando implementar autenticação, volte para o código acima.
             |--------------------------------------------------------------------------
             */
            ->query(Policy::query()->latest())
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
                    ->sortable(),

                TextColumn::make('insured.name')
                    ->label('Segurado')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge(),

                TextColumn::make('total_premium')
                    ->label('Prêmio Total')
                    ->money('BRL')
                    ->sortable(),

                TextColumn::make('end_date')
                    ->label('Vencimento')
                    ->date('d/m/Y')
                    ->sortable(),
            ])

            ->recordActions([
                ActionGroup::make([
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
                    ->options(
                        collect(PolicyStatusEnum::cases())->pluck('name', 'value')->toArray()
                    ),
            ])
            ->emptyStateHeading('Nenhum cliente em potencial encontrado')
            ->emptyStateDescription('Não encontramos registros correspondentes à pesquisa ou filtro selecionado.');

    }

    public function render()
    {
        return view('livewire.policy.list-all');
    }
}