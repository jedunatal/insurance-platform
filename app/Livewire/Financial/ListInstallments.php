<?php

namespace App\Livewire\Financial;

use App\Actions\Financial\SettleInstallmentAction;
use App\Enums\FinancialStatusEnum;
use App\Models\PolicyInstallment;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Gestão Financeira & Comissões')]
#[Layout('layouts.app')]
class ListInstallments extends Component implements HasTable, HasActions, HasForms
{
    use InteractsWithTable;
    use InteractsWithActions;
    use InteractsWithForms;

    public function table(Table $table): Table
    {
        return $table
            ->query(
                PolicyInstallment::query()
                    ->with(['policy.insured', 'insured'])
                    ->latest('due_date')
            )
            ->columns([
                TextColumn::make('installment_number')
                    ->label('Parcela')
                    ->formatStateUsing(fn (PolicyInstallment $record): string => $record->formattedInstallment())
                    ->sortable(),

                TextColumn::make('policy.policy_number')
                    ->label('Apólice / Seguradora')
                    ->searchable()
                    ->sortable()
                    ->description(fn (PolicyInstallment $record): string => $record->policy?->insurer ?? 'Seguradora N/I'),

                TextColumn::make('insured.name')
                    ->label('Segurado')
                    ->searchable()
                    ->sortable()
                    ->default(fn (PolicyInstallment $record): string => $record->policy?->insured?->name ?? '-'),

                TextColumn::make('due_date')
                    ->label('Vencimento')
                    ->date('d/m/Y')
                    ->sortable(),

                TextColumn::make('payment_date')
                    ->label('Data Pgto.')
                    ->date('d/m/Y')
                    ->placeholder('Em aberto')
                    ->sortable(),

                TextColumn::make('gross_amount')
                    ->label('Valor Parcela')
                    ->money('BRL')
                    ->sortable(),

                TextColumn::make('commission_expected')
                    ->label('Comissão Prevista')
                    ->money('BRL')
                    ->sortable(),

                TextColumn::make('commission_received')
                    ->label('Comissão Recebida')
                    ->money('BRL')
                    ->placeholder('-')
                    ->sortable(),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge(),
            ])
            ->recordActions([
                ActionGroup::make([
                    Action::make('settle')
                        ->label('Liquidar / Confirmar Recebimento')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->visible(fn (PolicyInstallment $record): bool => $record->status !== FinancialStatusEnum::Paid)
                        ->modalHeading('Liquidar Parcela e Confirmar Comissão')
                        ->modalDescription('Informe os dados de pagamento e a comissão efetivamente recebida.')
                        ->form([
                            DatePicker::make('payment_date')
                                ->label('Data do Pagamento')
                                ->default(now())
                                ->required(),

                            TextInput::make('commission_received')
                                ->label('Comissão Efetivamente Recebida')
                                ->prefix('R$')
                                ->numeric()
                                ->default(fn (PolicyInstallment $record): float => (float) $record->commission_expected)
                                ->required(),

                            Textarea::make('notes')
                                ->label('Observações do Recebimento')
                                ->placeholder('Detalhes da quitação ou retenção de impostos...')
                                ->rows(2),
                        ])
                        ->action(function (PolicyInstallment $record, array $data): void {
                            app(SettleInstallmentAction::class)->execute(
                                $record,
                                $data['payment_date'],
                                (float) $data['commission_received'],
                                $data['notes'] ?? null
                            );

                            Notification::make()
                                ->title('Parcela liquidada com sucesso!')
                                ->body("Comissão de R$ " . number_format((float) $data['commission_received'], 2, ',', '.') . " registrada.")
                                ->success()
                                ->send();
                        }),

                    Action::make('view_policy')
                        ->label('Visualizar Apólice')
                        ->icon('heroicon-o-eye')
                        ->color('primary')
                        ->url(fn (PolicyInstallment $record): string => route('policies.view', $record->policy_id)),
                ])
                ->label('Ações'),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Filtrar por Status')
                    ->options(FinancialStatusEnum::options()),

                Filter::make('due_this_month')
                    ->label('Vencendo este mês')
                    ->query(fn (Builder $query): Builder => $query->whereMonth('due_date', now()->month)->whereYear('due_date', now()->year)),

                Filter::make('overdue')
                    ->label('Parcelas em Atraso')
                    ->query(fn (Builder $query): Builder => $query->where(function ($q) {
                        $q->where('status', FinancialStatusEnum::Overdue->value)
                          ->orWhere(function ($sub) {
                              $sub->where('status', FinancialStatusEnum::Pending->value)
                                  ->where('due_date', '<', today());
                          });
                    })),
            ])
            ->emptyStateHeading('Nenhum lançamento financeiro encontrado')
            ->emptyStateDescription('Emita apólices com plano de parcelamento para visualizar a grade de parcelas e comissões.');
    }

    public function render()
    {
        $tenantId = auth()->user()?->tenant_id;

        $baseQuery = PolicyInstallment::query()->when($tenantId, fn ($q) => $q->where('tenant_id', $tenantId));

        $totalExpectedCommissions = (float) (clone $baseQuery)->where('status', FinancialStatusEnum::Pending->value)->sum('commission_expected');
        $totalReceivedCommissions = (float) (clone $baseQuery)->where('status', FinancialStatusEnum::Paid->value)->sum('commission_received');
        $totalPendingGross = (float) (clone $baseQuery)->where('status', FinancialStatusEnum::Pending->value)->sum('gross_amount');
        $totalOverdueCount = (clone $baseQuery)->where(function ($q) {
            $q->where('status', FinancialStatusEnum::Overdue->value)
              ->orWhere(function ($sub) {
                  $sub->where('status', FinancialStatusEnum::Pending->value)
                      ->where('due_date', '<', today());
              });
        })->count();

        return view('livewire.financial.list-installments', [
            'metrics' => [
                'expected_commissions' => $totalExpectedCommissions,
                'received_commissions' => $totalReceivedCommissions,
                'pending_gross'        => $totalPendingGross,
                'overdue_count'        => $totalOverdueCount,
            ],
        ]);
    }
}
