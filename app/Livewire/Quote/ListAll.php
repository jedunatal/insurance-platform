<?php

namespace App\Livewire\Quote;

use App\Actions\Quote\ConvertQuoteToPolicyAction;
use App\Enums\InsuranceBranchEnum;
use App\Enums\QuoteStatusEnum;
use App\Models\Quote;
use App\Models\QuoteOption;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Actions\CreateAction;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Cotações & Propostas')]
#[Layout('layouts.app')]
class ListAll extends Component implements HasTable, HasActions, HasForms
{
    use InteractsWithTable;
    use InteractsWithActions;
    use InteractsWithForms;

    public function table(Table $table): Table
    {
        return $table
            ->query(Quote::query()->with(['lead', 'insured', 'options'])->latest())
            ->headerActions([
                CreateAction::make('create')
                    ->label('Nova Cotação Multi-Cálculo')
                    ->icon('heroicon-o-plus')
                    ->url(route('quotes.create'))
                    ->extraAttributes([
                        'class' => '!bg-[#295384] hover:!bg-[#1c385a] !text-white [&_svg]:!text-white font-medium transition-colors shadow-xs',
                    ]),
            ])
            ->columns([
                TextColumn::make('quote_number')
                    ->label('Nº Cotação')
                    ->searchable()
                    ->sortable()
                    ->description(fn (Quote $record): string => $record->title),

                TextColumn::make('client_name')
                    ->label('Cliente / Lead')
                    ->state(fn (Quote $record): string => $record->insured?->name ?? ($record->lead?->name ?? 'Não vinculado'))
                    ->description(fn (Quote $record): string => "Ramo: " . ($record->branch?->getLabel() ?? $record->branch))
                    ->searchable(),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge(),

                TextColumn::make('best_option')
                    ->label('Opções Cotadas')
                    ->state(function (Quote $record): string {
                        $count = $record->options->count();
                        $best = $record->recommendedOption();

                        return $best ? "{$count} seguradoras (a partir de {$best->formattedTotalPremium()})" : "{$count} opções";
                    }),

                TextColumn::make('valid_until')
                    ->label('Validade')
                    ->date('d/m/Y')
                    ->sortable(),
            ])
            ->recordActions([
                ActionGroup::make([
                    Action::make('view_comparison')
                        ->label('Ver Comparativo')
                        ->icon('heroicon-o-eye')
                        ->color('primary')
                        ->url(fn (Quote $record): string => route('quotes.view', $record)),

                    Action::make('download_pdf')
                        ->label('Baixar PDF da Proposta')
                        ->icon('heroicon-o-arrow-down-tray')
                        ->color('success')
                        ->url(fn (Quote $record): string => route('quotes.document.download', $record))
                        ->openUrlInNewTab(),

                    Action::make('convert_to_policy')
                        ->label('Aprovar e Emitir Apólice')
                        ->icon('heroicon-o-check-badge')
                        ->color('warning')
                        ->visible(fn (Quote $record): bool => $record->status !== QuoteStatusEnum::Converted && $record->options->isNotEmpty())
                        ->requiresConfirmation()
                        ->modalHeading('Converter Cotação em Apólice')
                        ->modalDescription('Selecione a opção de seguradora aceita pelo cliente para emitir o contrato.')
                        ->action(function (Quote $record): void {
                            $best = $record->recommendedOption();
                            if (! $best) {
                                Notification::make()->title('Nenhuma opção de seguradora encontrada!')->danger()->send();

                                return;
                            }

                            $policy = app(ConvertQuoteToPolicyAction::class)->execute($record, $best);

                            Notification::make()
                                ->title('Cotação Convertida em Apólice!')
                                ->body("Apólice #{$policy->policy_number} emitida com sucesso.")
                                ->success()
                                ->send();
                        }),
                ])
                ->label('Ações'),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Filtrar por Status')
                    ->options(QuoteStatusEnum::options()),

                SelectFilter::make('branch')
                    ->label('Filtrar por Ramo')
                    ->options(InsuranceBranchEnum::options()),
            ])
            ->emptyStateHeading('Nenhuma cotação cadastrada')
            ->emptyStateDescription('Crie cotações comparativas multi-seguradoras para enviar propostas comerciais aos seus clientes.');
    }

    public function render()
    {
        return view('livewire.quote.list-all');
    }
}
