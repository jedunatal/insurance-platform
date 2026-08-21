<?php

namespace App\Livewire\Policy;

use App\DTOs\PolicyData;
use App\Enums\InsuranceBranchEnum;
use App\Enums\PaymentMethodEnum;
use App\Enums\PolicyStatusEnum;
use App\Models\Insured;
use App\Models\Policy;
use App\Models\User;
use App\Services\Insurance\PolicyService;
use App\Support\CurrencyHelper;
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
                        ->extraAttributes(['class' => 'relative z-30 overflow-visible'])
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
                                    'Porto Seguro'    => 'Porto Seguro',
                                    'Azul Seguros'    => 'Azul Seguros',
                                    'Bradesco Seguros'=> 'Bradesco Seguros',
                                    'Allianz Seguros' => 'Allianz Seguros',
                                    'Tokio Marine'    => 'Tokio Marine',
                                    'SulAmérica'      => 'SulAmérica',
                                    'Mapfre Seguros'  => 'Mapfre Seguros',
                                    'Zurich Seguros'  => 'Zurich Seguros',
                                    'HDI Seguros'     => 'HDI Seguros',
                                    'Sompo Seguros'   => 'Sompo Seguros',
                                    'Liberty Seguros' => 'Liberty Seguros',
                                ])
                                ->searchable()
                                ->columnSpan(['default' => 12, 'md' => 6]),

                            Select::make('branch')
                                ->label('Ramo do Seguro')
                                ->options(InsuranceBranchEnum::options())
                                ->searchable()
                                ->live()
                                ->required()
                                ->afterStateUpdated(function ($state, $set, $get) {
                                    if ($state) {
                                        $branchEnum = InsuranceBranchEnum::tryFrom($state);
                                        if ($branchEnum) {
                                            $rate = $branchEnum->defaultIofRate();
                                            $set('iof_rate', $rate);

                                            $net = CurrencyHelper::parse($get('net_premium'));
                                            $iof = round($net * ($rate / 100), 2);
                                            $set('iof_amount', number_format($iof, 2, ',', '.'));
                                            $set('total_premium', number_format(round($net + $iof, 2), 2, ',', '.'));

                                            $commRate = (float) (str_replace(',', '.', (string) ($get('commission_percentage') ?? 0)));
                                            $commAmount = round($net * ($commRate / 100), 2);
                                            $set('commission_amount', number_format($commAmount, 2, ',', '.'));

                                            $producerRate = (float) (str_replace(',', '.', (string) ($get('producer_commission_percentage') ?? 0)));
                                            $set('producer_commission_amount', number_format(round($commAmount * ($producerRate / 100), 2), 2, ',', '.'));
                                        }
                                    }
                                })
                                ->columnSpan(['default' => 12, 'md' => 4]),

                            Select::make('product_id')
                                ->label('Produto / Catálogo')
                                ->placeholder('Selecione o produto...')
                                ->options(fn ($get) => app(PolicyService::class)->productOptions($this->resolveTenantId(), $get('branch')))
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
                        ->extraAttributes(['class' => 'relative z-20 overflow-visible'])
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

                    // 3. Dados Específicos por Ramo de Seguro (Automóvel, Imóvel ou Vida)
                    Section::make('Dados do Veículo (Automóvel)')
                        ->extraAttributes(['class' => 'relative z-18 overflow-visible'])
                        ->description('Preencha os detalhes técnicos do veículo para apólices do ramo Auto.')
                        ->schema([
                            Grid::make(12)->schema([
                                TextInput::make('vehicle_data.plate')
                                    ->label('Placa do Veículo')
                                    ->placeholder('Ex: ABC-1D23')
                                    ->columnSpan(['default' => 12, 'md' => 3]),

                                TextInput::make('vehicle_data.fipe_code')
                                    ->label('Código FIPE')
                                    ->placeholder('Ex: 005389-9')
                                    ->columnSpan(['default' => 12, 'md' => 3]),

                                TextInput::make('vehicle_data.brand')
                                    ->label('Marca / Montadora')
                                    ->placeholder('Ex: Toyota, Honda, VW')
                                    ->columnSpan(['default' => 12, 'md' => 3]),

                                TextInput::make('vehicle_data.model')
                                    ->label('Modelo / Versão')
                                    ->placeholder('Ex: Corolla XEi 2.0 Flex')
                                    ->columnSpan(['default' => 12, 'md' => 3]),

                                TextInput::make('vehicle_data.model_year')
                                    ->label('Ano Fab / Modelo')
                                    ->placeholder('Ex: 2024/2025')
                                    ->columnSpan(['default' => 12, 'md' => 3]),

                                TextInput::make('vehicle_data.chassis')
                                    ->label('Chassi')
                                    ->placeholder('Ex: 9BWCA05Z89P000000')
                                    ->columnSpan(['default' => 12, 'md' => 3]),

                                TextInput::make('vehicle_data.overnight_zip_code')
                                    ->label('CEP de Pernoite')
                                    ->placeholder('00000-000')
                                    ->mask('99999-999')
                                    ->columnSpan(['default' => 12, 'md' => 3]),

                                TextInput::make('vehicle_data.main_driver')
                                    ->label('Condutor Principal')
                                    ->placeholder('Ex: João da Silva (Titular)')
                                    ->columnSpan(['default' => 12, 'md' => 3]),
                            ]),
                        ])
                        ->collapsible()
                        ->columnSpan(12),

                    Section::make('Dados do Imóvel / Risco (Residencial / Empresarial)')
                        ->extraAttributes(['class' => 'relative z-16 overflow-visible'])
                        ->description('Informações sobre a localidade e características estruturais do imóvel segurado.')
                        ->schema([
                            Grid::make(12)->schema([
                                Select::make('property_data.property_type')
                                    ->label('Tipo de Imóvel')
                                    ->options([
                                        'casa'        => 'Casa / Sobrado',
                                        'apartamento' => 'Apartamento',
                                        'comercial'   => 'Sala Comercial / Galpão',
                                        'industrial'  => 'Galpão Industrial',
                                    ])
                                    ->columnSpan(['default' => 12, 'md' => 3]),

                                TextInput::make('property_data.risk_zip_code')
                                    ->label('CEP do Risco')
                                    ->placeholder('00000-000')
                                    ->mask('99999-999')
                                    ->columnSpan(['default' => 12, 'md' => 3]),

                                Select::make('property_data.construction_type')
                                    ->label('Tipo de Construção')
                                    ->options([
                                        'alvenaria' => 'Alvenaria Superior',
                                        'madeira'   => 'Madeira / Mista',
                                    ])
                                    ->columnSpan(['default' => 12, 'md' => 3]),

                                Select::make('property_data.has_alarm')
                                    ->label('Alarme / Monitoramento')
                                    ->options([
                                        'sim' => 'Sim (Monitorado 24h)',
                                        'nao' => 'Não possui',
                                    ])
                                    ->columnSpan(['default' => 12, 'md' => 3]),
                            ]),
                        ])
                        ->collapsible()
                        ->columnSpan(12),

                    Section::make('Quadro de Beneficiários (Vida & Acidentes Pessoais)')
                        ->extraAttributes(['class' => 'relative z-14 overflow-visible'])
                        ->description('Indicação expressa de beneficiários e respectiva partilha percentual.')
                        ->schema([
                            Repeater::make('beneficiaries')
                                ->label('Beneficiários Cadastrados')
                                ->schema([
                                    TextInput::make('name')
                                        ->label('Nome Completo')
                                        ->placeholder('Ex: Maria Silva')
                                        ->required()
                                        ->columnSpan(['default' => 12, 'md' => 4]),

                                    TextInput::make('relationship')
                                        ->label('Parentesco')
                                        ->placeholder('Ex: Cônjuge, Filho(a)')
                                        ->columnSpan(['default' => 12, 'md' => 3]),

                                    TextInput::make('document')
                                        ->label('CPF')
                                        ->placeholder('000.000.000-00')
                                        ->mask('999.999.999-99')
                                        ->columnSpan(['default' => 12, 'md' => 3]),

                                    TextInput::make('share_percentage')
                                        ->label('Participação (%)')
                                        ->suffix('%')
                                        ->numeric()
                                        ->default(100)
                                        ->columnSpan(['default' => 12, 'md' => 2]),
                                ])
                                ->columns(12)
                                ->defaultItems(0)
                                ->addActionLabel('+ Adicionar Beneficiário')
                                ->collapsible()
                                ->columnSpanFull(),
                        ])
                        ->collapsible()
                        ->columnSpan(12),

                    // 4. Valores, Tributos, Comissões e Pagamento
                    Section::make('Valores e Condições de Pagamento')
                        ->extraAttributes(['class' => 'relative z-12 overflow-visible'])
                        ->schema([
                            TextInput::make('net_premium')
                                ->label('Prêmio Líquido')
                                ->prefix('R$')
                                ->placeholder('0,00')
                                ->default('0,00')
                                ->extraInputAttributes([
                                    'x-mask:dynamic' => '$money($input, \',\', \'.\', 2)',
                                ])
                                ->required()
                                ->live(onBlur: true)
                                ->afterStateUpdated(function ($state, $set, $get) {
                                    $net = CurrencyHelper::parse($state);
                                    $rate = (float) (str_replace(',', '.', (string) ($get('iof_rate') ?? 7.38)));
                                    $iof = round($net * ($rate / 100), 2);
                                    $commRate = (float) (str_replace(',', '.', (string) ($get('commission_percentage') ?? 0)));
                                    $comm = round($net * ($commRate / 100), 2);

                                    $set('iof_amount', number_format($iof, 2, ',', '.'));
                                    $set('total_premium', number_format(round($net + $iof, 2), 2, ',', '.'));
                                    $set('commission_amount', number_format($comm, 2, ',', '.'));

                                    $prodRate = (float) (str_replace(',', '.', (string) ($get('producer_commission_percentage') ?? 0)));
                                    $set('producer_commission_amount', number_format(round($comm * ($prodRate / 100), 2), 2, ',', '.'));
                                })
                                ->columnSpan(['default' => 12, 'md' => 4]),

                            TextInput::make('iof_rate')
                                ->label('Alíquota IOF')
                                ->numeric()
                                ->suffix('%')
                                ->default(7.38)
                                ->live(onBlur: true)
                                ->afterStateUpdated(function ($state, $set, $get) {
                                    $net = CurrencyHelper::parse($get('net_premium'));
                                    $rate = (float) (str_replace(',', '.', (string) ($state ?? 0)));
                                    $iof = round($net * ($rate / 100), 2);

                                    $set('iof_amount', number_format($iof, 2, ',', '.'));
                                    $set('total_premium', number_format(round($net + $iof, 2), 2, ',', '.'));
                                })
                                ->columnSpan(['default' => 12, 'md' => 4]),

                            TextInput::make('iof_amount')
                                ->label('Valor do IOF')
                                ->prefix('R$')
                                ->placeholder('0,00')
                                ->default('0,00')
                                ->extraInputAttributes([
                                    'x-mask:dynamic' => '$money($input, \',\', \'.\', 2)',
                                ])
                                ->live(onBlur: true)
                                ->afterStateUpdated(function ($state, $set, $get) {
                                    $net = CurrencyHelper::parse($get('net_premium'));
                                    $iof = CurrencyHelper::parse($state);
                                    $set('total_premium', number_format(round($net + $iof, 2), 2, ',', '.'));
                                })
                                ->columnSpan(['default' => 12, 'md' => 4]),

                            TextInput::make('total_premium')
                                ->label('Prêmio Total')
                                ->prefix('R$')
                                ->placeholder('0,00')
                                ->default('0,00')
                                ->extraInputAttributes([
                                    'x-mask:dynamic' => '$money($input, \',\', \'.\', 2)',
                                ])
                                ->readOnly()
                                ->columnSpan(['default' => 12, 'md' => 4]),

                            TextInput::make('commission_percentage')
                                ->label('Comissão da Corretora')
                                ->numeric()
                                ->suffix('%')
                                ->default(0)
                                ->live(onBlur: true)
                                ->afterStateUpdated(function ($state, $set, $get) {
                                    $net = CurrencyHelper::parse($get('net_premium'));
                                    $commRate = (float) (str_replace(',', '.', (string) ($state ?? 0)));
                                    $comm = round($net * ($commRate / 100), 2);
                                    $set('commission_amount', number_format($comm, 2, ',', '.'));

                                    $prodRate = (float) (str_replace(',', '.', (string) ($get('producer_commission_percentage') ?? 0)));
                                    $set('producer_commission_amount', number_format(round($comm * ($prodRate / 100), 2), 2, ',', '.'));
                                })
                                ->columnSpan(['default' => 12, 'md' => 4]),

                            TextInput::make('commission_amount')
                                ->label('Comissão Prevista')
                                ->prefix('R$')
                                ->placeholder('0,00')
                                ->default('0,00')
                                ->extraInputAttributes([
                                    'x-mask:dynamic' => '$money($input, \',\', \'.\', 2)',
                                ])
                                ->readOnly()
                                ->columnSpan(['default' => 12, 'md' => 4]),

                            TextInput::make('deductible_amount')
                                ->label('Valor da Franquia Principal')
                                ->prefix('R$')
                                ->placeholder('0,00')
                                ->default('0,00')
                                ->extraInputAttributes([
                                    'x-mask:dynamic' => '$money($input, \',\', \'.\', 2)',
                                ])
                                ->columnSpan(['default' => 12, 'md' => 4]),

                            Select::make('payment_method')
                                ->label('Forma de Pagamento')
                                ->options(PaymentMethodEnum::options())
                                ->default(PaymentMethodEnum::Invoice->value)
                                ->required()
                                ->columnSpan(['default' => 12, 'md' => 4]),

                            TextInput::make('installments_count')
                                ->label('Quantidade de Parcelas')
                                ->numeric()
                                ->default(1)
                                ->minValue(1)
                                ->maxValue(36)
                                ->required()
                                ->columnSpan(['default' => 12, 'md' => 4]),
                        ])
                        ->columns(12)
                        ->columnSpan(12),

                    // 5. Split de Comissões com Produtor / Parceiro
                    Section::make('Split de Comissão / Repasse a Produtor (Opcional)')
                        ->extraAttributes(['class' => 'relative z-10 overflow-visible'])
                        ->description('Configure o rateio de comissão para corretores parceiros ou produtores comerciais.')
                        ->schema([
                            Select::make('producer_id')
                                ->label('Produtor / Parceiro Comercial')
                                ->options(fn () => User::where('tenant_id', auth()->user()?->tenant_id ?? 1)->where('is_active', true)->pluck('name', 'id'))
                                ->searchable()
                                ->preload()
                                ->nullable()
                                ->columnSpan(['default' => 12, 'md' => 4]),

                            TextInput::make('producer_commission_percentage')
                                ->label('Percentual de Repasse (%)')
                                ->numeric()
                                ->suffix('%')
                                ->default(0)
                                ->live(onBlur: true)
                                ->afterStateUpdated(function ($state, $set, $get) {
                                    $comm = CurrencyHelper::parse($get('commission_amount'));
                                    $prodRate = (float) (str_replace(',', '.', (string) ($state ?? 0)));
                                    $set('producer_commission_amount', number_format(round($comm * ($prodRate / 100), 2), 2, ',', '.'));
                                })
                                ->columnSpan(['default' => 12, 'md' => 4]),

                            TextInput::make('producer_commission_amount')
                                ->label('Valor do Repasse ao Produtor')
                                ->prefix('R$')
                                ->placeholder('0,00')
                                ->default('0,00')
                                ->extraInputAttributes([
                                    'x-mask:dynamic' => '$money($input, \',\', \'.\', 2)',
                                ])
                                ->readOnly()
                                ->columnSpan(['default' => 12, 'md' => 4]),
                        ])
                        ->columns(12)
                        ->collapsible()
                        ->columnSpan(12),

                    // 6. Coberturas Flexíveis
                    Section::make('Coberturas Contratadas')
                        ->extraAttributes(['class' => 'relative z-6 overflow-visible'])
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

                    // 7. Anotações
                    Section::make('Anotações Gerais')
                        ->extraAttributes(['class' => 'relative z-1 overflow-visible'])
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
