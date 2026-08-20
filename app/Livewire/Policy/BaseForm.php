<?php

namespace App\Livewire\Policy;

use App\DTOs\PolicyData;
use App\Enums\InsuranceBranchEnum;
use App\Enums\PolicyPaymentMethodEnum;
use App\Enums\PolicyStatusEnum;
use App\Models\Insured;
use App\Models\Policy;
use App\Services\Insurance\PolicyService;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Illuminate\Support\Facades\DB;

class BaseForm
{
    public static function getFields(): array
    {
        return (new self)->fields();
    }

    public function fields(): array
    {
        return [
            Grid::make(12)
                ->schema([
                    // 1. Identificação do Contrato
                    Section::make('Identificação da Apólice e Segurado')
                        ->schema([
                            Select::make('insured_id')
                                ->label('Segurado')
                                ->options(fn () => app(PolicyService::class)->insuredOptions($this->resolveTenantId()))
                                ->searchable()
                                ->preload()
                                ->required()
                                ->columnSpan(['default' => 12, 'md' => 6]),

                            Select::make('insurer')
                                ->label('Seguradora')
                                ->placeholder('Selecione ou digite a seguradora')
                                ->options([
                                    'Porto Seguro' => 'Porto Seguro',
                                    'Azul Seguros' => 'Azul Seguros',
                                    'Bradesco Seguros' => 'Bradesco Seguros',
                                    'Allianz Seguros' => 'Allianz Seguros',
                                    'Tokio Marine' => 'Tokio Marine',
                                    'SulAmérica' => 'SulAmérica',
                                    'Mapfre Seguros' => 'Mapfre Seguros',
                                    'Zurich Seguros' => 'Zurich Seguros',
                                    'HDI Seguros' => 'HDI Seguros',
                                    'Sompo Seguros' => 'Sompo Seguros',
                                    'Liberty Seguros' => 'Liberty Seguros',
                                ])
                                ->searchable()
                                ->columnSpan(['default' => 12, 'md' => 6]),

                            Select::make('branch')
                                ->label('Ramo do Seguro')
                                ->options(InsuranceBranchEnum::options())
                                ->searchable()
                                ->required()
                                ->columnSpan(['default' => 12, 'md' => 4]),

                            Select::make('product_id')
                                ->label('Produto / Catálogo')
                                ->options(fn () => app(PolicyService::class)->productOptions($this->resolveTenantId()))
                                ->searchable()
                                ->preload()
                                ->nullable()
                                ->columnSpan(['default' => 12, 'md' => 4]),

                            Select::make('broker_id')
                                ->label('Corretor Responsável')
                                ->options(fn () => app(PolicyService::class)->brokerOptions($this->resolveTenantId()))
                                ->searchable()
                                ->preload()
                                ->nullable()
                                ->columnSpan(['default' => 12, 'md' => 4]),

                            TextInput::make('policy_number')
                                ->label('Número da Apólice')
                                ->placeholder('Ex: 01.031.1234567')
                                ->required()
                                ->columnSpan(['default' => 12, 'md' => 4]),

                            TextInput::make('proposal_number')
                                ->label('Número da Proposta')
                                ->placeholder('Ex: PROP-2026-001')
                                ->nullable()
                                ->columnSpan(['default' => 12, 'md' => 4]),

                            Select::make('status')
                                ->label('Status da Apólice')
                                ->options(PolicyStatusEnum::options())
                                ->default(PolicyStatusEnum::Active->value)
                                ->required()
                                ->columnSpan(['default' => 12, 'md' => 4]),
                        ])
                        ->columns(12)
                        ->columnSpan(12),

                    // 2. Vigência
                    Section::make('Vigência do Seguro')
                        ->schema([
                            DatePicker::make('start_date')
                                ->label('Início da Vigência')
                                ->native(false)
                                ->displayFormat('d/m/Y')
                                ->required()
                                ->live()
                                ->columnSpan(['default' => 12, 'md' => 6]),

                            DatePicker::make('end_date')
                                ->label('Fim da Vigência')
                                ->native(false)
                                ->displayFormat('d/m/Y')
                                ->required()
                                ->after('start_date')
                                ->columnSpan(['default' => 12, 'md' => 6]),
                        ])
                        ->columns(12)
                        ->columnSpan(12),

                    // 3. Valores e Pagamento
                    Section::make('Valores e Condições de Pagamento')
                        ->schema([
                            TextInput::make('net_premium')
                                ->label('Prêmio Líquido')
                                ->numeric()
                                ->prefix('R$')
                                ->default(0)
                                ->required()
                                ->live(onBlur: true)
                                ->afterStateUpdated(function ($state, $set, $get) {
                                    $net = (float) ($state ?? 0);
                                    $iof = round($net * 0.0738, 2);
                                    $set('iof_amount', $iof);
                                    $set('total_premium', round($net + $iof, 2));
                                })
                                ->columnSpan(['default' => 12, 'md' => 3]),

                            TextInput::make('iof_amount')
                                ->label('IOF (7,38%)')
                                ->numeric()
                                ->prefix('R$')
                                ->default(0)
                                ->live(onBlur: true)
                                ->afterStateUpdated(function ($state, $set, $get) {
                                    $net = (float) ($get('net_premium') ?? 0);
                                    $iof = (float) ($state ?? 0);
                                    $set('total_premium', round($net + $iof, 2));
                                })
                                ->columnSpan(['default' => 12, 'md' => 3]),

                            TextInput::make('total_premium')
                                ->label('Prêmio Total')
                                ->numeric()
                                ->prefix('R$')
                                ->default(0)
                                ->required()
                                ->columnSpan(['default' => 12, 'md' => 3]),

                            TextInput::make('deductible_amount')
                                ->label('Valor da Franquia Principal')
                                ->numeric()
                                ->prefix('R$')
                                ->default(0)
                                ->columnSpan(['default' => 12, 'md' => 3]),

                            Select::make('payment_method')
                                ->label('Forma de Pagamento')
                                ->options(PolicyPaymentMethodEnum::options())
                                ->default(PolicyPaymentMethodEnum::Invoice->value)
                                ->required()
                                ->columnSpan(['default' => 12, 'md' => 6]),

                            TextInput::make('installments_count')
                                ->label('Quantidade de Parcelas')
                                ->numeric()
                                ->default(1)
                                ->minValue(1)
                                ->required()
                                ->columnSpan(['default' => 12, 'md' => 6]),
                        ])
                        ->columns(12)
                        ->columnSpan(12),

                    // 4. Coberturas Flexíveis
                    Section::make('Coberturas Contratadas')
                        ->description('Adicione as coberturas, limites máximos de indenização (LMI) e franquias específicas.')
                        ->schema([
                            Repeater::make('coverages')
                                ->label('Lista de Coberturas')
                                ->schema([
                                    TextInput::make('name')
                                        ->label('Cobertura')
                                        ->placeholder('Ex: Compreensiva / Casco, RCF-V Danos Materiais, Vidros')
                                        ->required()
                                        ->columnSpan(['default' => 12, 'md' => 5]),

                                    TextInput::make('limit_amount')
                                        ->label('Limite Máximo (LMI)')
                                        ->placeholder('Ex: R$ 100.000,00 ou 100% FIPE')
                                        ->columnSpan(['default' => 12, 'md' => 4]),

                                    TextInput::make('deductible')
                                        ->label('Franquia Específica')
                                        ->placeholder('Ex: R$ 2.500,00 ou Isenta')
                                        ->columnSpan(['default' => 12, 'md' => 3]),
                                ])
                                ->columns(12)
                                ->defaultItems(1)
                                ->addActionLabel('+ Adicionar Cobertura')
                                ->collapsible()
                                ->columnSpanFull(),
                        ])
                        ->columnSpan(12),

                    // 5. Anotações
                    Section::make('Anotações Gerais')
                        ->schema([
                            Textarea::make('notes')
                                ->label('Observações')
                                ->placeholder('Observações, cláusulas particulares ou detalhes sobre a contratação...')
                                ->rows(3)
                                ->columnSpanFull(),
                        ])
                        ->collapsible()
                        ->columnSpan(12),
                ]),
        ];
    }

    public static function create(array $data): ?Policy
    {
        return DB::transaction(function () use ($data) {
            $data['tenant_id'] = auth()->user()?->tenant_id ?? 1;
            $data['created_by'] = auth()->id();

            $dto = PolicyData::fromArray($data);

            return app(PolicyService::class)->create($dto);
        });
    }

    private function resolveTenantId(): int
    {
        $tenantId = auth()->user()?->tenant_id;

        return $tenantId !== null ? (int) $tenantId : 0;
    }
}
