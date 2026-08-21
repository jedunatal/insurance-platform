<?php

namespace App\Livewire\Quote;

use App\Actions\Quote\CreateQuoteAction;
use App\Enums\InsuranceBranchEnum;
use App\Enums\QuoteStatusEnum;
use App\Models\Insured;
use App\Models\Lead;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Nova Cotação Multi-Seguradoras')]
#[Layout('layouts.app')]
class Create extends Component implements HasForms, HasActions
{
    use InteractsWithForms;
    use InteractsWithActions;

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'title'       => 'Estudo de Cotação de Seguro',
            'branch'      => InsuranceBranchEnum::Auto->value,
            'status'      => QuoteStatusEnum::Draft->value,
            'valid_until' => now()->addDays(15)->toDateString(),
            'options'     => [], // Inicia vazio para o corretor adicionar as opções reais via Repeater
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->statePath('data')
            ->schema([
                Section::make('Identificação da Cotação')
                    ->extraAttributes(['class' => 'relative z-20 overflow-visible'])
                    ->schema([
                        Grid::make(3)->schema([
                            TextInput::make('title')
                                ->label('Título da Proposta / Estudo')
                                ->default('Estudo de Cotação de Seguro')
                                ->required(),

                            Select::make('branch')
                                ->label('Ramo do Seguro')
                                ->options(InsuranceBranchEnum::options())
                                ->required(),

                            DatePicker::make('valid_until')
                                ->label('Validade da Cotação')
                                ->default(now()->addDays(15))
                                ->required(),
                        ]),

                        Grid::make(2)->schema([
                            Select::make('insured_id')
                                ->label('Vincular a Segurado Existente (Opcional)')
                                ->options(Insured::pluck('name', 'id'))
                                ->searchable(),

                            Select::make('lead_id')
                                ->label('Ou vincular a um Lead (Opcional)')
                                ->options(Lead::pluck('name', 'id'))
                                ->searchable(),
                        ]),

                        Textarea::make('notes')
                            ->label('Observações do Perfil de Risco')
                            ->placeholder('Ex: Veículo utilizado para ida e volta do trabalho, pernoite em garagem fechada...')
                            ->rows(2),
                    ]),

                Section::make('Opções de Seguradoras para Comparativo')
                    ->extraAttributes(['class' => 'relative z-10 overflow-visible'])
                    ->description('Adicione as alternativas de cálculo das seguradoras parceiras para gerar a comparação.')
                    ->schema([
                        Repeater::make('options')
                            ->label('Opções de Cálculo')
                            ->schema([
                                Grid::make(3)->schema([
                                    TextInput::make('insurer')
                                        ->label('Seguradora')
                                        ->placeholder('Ex: Porto Seguro, Allianz, Tokio Marine')
                                        ->required(),

                                    TextInput::make('total_premium')
                                        ->label('Prêmio Total')
                                        ->prefix('R$')
                                        ->placeholder('0,00')
                                        ->extraInputAttributes([
                                            'x-mask:dynamic' => '$money($input, \',\', \'.\', 2)',
                                        ])
                                        ->required(),

                                    TextInput::make('deductible_amount')
                                        ->label('Franquia Principal')
                                        ->prefix('R$')
                                        ->placeholder('0,00')
                                        ->extraInputAttributes([
                                            'x-mask:dynamic' => '$money($input, \',\', \'.\', 2)',
                                        ])
                                        ->required(),
                                ]),

                                Grid::make(3)->schema([
                                    TextInput::make('car_rental')
                                        ->label('Carro Reserva')
                                        ->placeholder('Ex: 15 dias (Sedan)'),

                                    TextInput::make('glass_coverage')
                                        ->label('Vidros / Faróis')
                                        ->placeholder('Ex: Completa'),

                                    Toggle::make('is_recommended')
                                        ->label('Destaque como Recomendada')
                                        ->inline(false),
                                ]),

                                TextInput::make('highlights')
                                    ->label('Diferenciais da Opção')
                                    ->placeholder('Ex: Guincho ilimitado, cobertura de vidros sem franquia...')
                                    ->columnSpanFull(),
                            ])
                            ->defaultItems(0)
                            ->addActionLabel('Adicionar Opção de Seguradora'),
                    ]),
            ]);
    }

    public function create()
    {
        $validated = $this->form->getState();
        $options = $validated['options'] ?? [];
        unset($validated['options']);

        $quote = app(CreateQuoteAction::class)->execute($validated, $options);

        Notification::make()
            ->title('Cotação Criada com Sucesso!')
            ->body("Estudo #{$quote->quote_number} gerado com {$quote->options->count()} opções comparativas.")
            ->success()
            ->send();

        return redirect()->route('quotes.view', $quote);
    }

    public function render()
    {
        return view('livewire.quote.create');
    }
}
