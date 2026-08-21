<?php

namespace App\Livewire\Claim;

use App\Enums\ClaimStatusEnum;
use App\Enums\ClaimTypeEnum;
use App\Models\Claim;
use App\Models\Insured;
use App\Models\Policy;
use App\Models\Tenant;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Get;
use Filament\Forms\Set;
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
                    // 1. Vínculos do Sinistro
                    Section::make('Identificação e Vínculo Contratual')
                        ->extraAttributes(['class' => 'relative z-30 overflow-visible'])
                        ->description('Selecione o segurado e a apólice vigente para abertura do sinistro.')
                        ->schema([
                            Select::make('insured_id')
                                ->label('Segurado')
                                ->placeholder('Selecione o segurado...')
                                ->options(Insured::query()->orderBy('name')->pluck('name', 'id'))
                                ->searchable()
                                ->preload()
                                ->required()
                                ->live()
                                ->afterStateUpdated(function (?string $state, $set, $get) {
                                    // Se a apólice selecionada não pertence ao novo segurado, limpa a apólice
                                    $policyId = $get('policy_id');
                                    if ($policyId && $state) {
                                        $policy = Policy::find($policyId);
                                        if ($policy && (string) $policy->insured_id !== (string) $state) {
                                            $set('policy_id', null);
                                        }
                                    }
                                })
                                ->columnSpan(['default' => 12, 'md' => 6]),

                            Select::make('policy_id')
                                ->label('Apólice Vinculada')
                                ->placeholder('Selecione a apólice...')
                                ->options(function ($get) {
                                    $insuredId = $get('insured_id');

                                    return Policy::query()
                                        ->when($insuredId, fn ($query) => $query->where('insured_id', $insuredId))
                                        ->get()
                                        ->mapWithKeys(function (Policy $policy) {
                                            $label = "Nº {$policy->policy_number}";
                                            if ($policy->insurer) {
                                                $label .= " - {$policy->insurer}";
                                            }
                                            if ($policy->branch) {
                                                $label .= " ({$policy->branch})";
                                            }
                                            if ($policy->start_date && $policy->end_date) {
                                                $label .= " [Vigência: {$policy->start_date->format('d/m/Y')} a {$policy->end_date->format('d/m/Y')}]";
                                            }

                                            return [$policy->id => $label];
                                        });
                                })
                                ->searchable()
                                ->preload()
                                ->required()
                                ->live()
                                ->afterStateUpdated(function (?string $state, $set) {
                                    // Ao selecionar a apólice diretamente, sincroniza o segurado correspondente
                                    if ($state) {
                                        $policy = Policy::find($state);
                                        if ($policy && $policy->insured_id) {
                                            $set('insured_id', $policy->insured_id);
                                            if ($policy->deductible_amount > 0) {
                                                $set('deductible_amount', (float) $policy->deductible_amount);
                                            }
                                        }
                                    }
                                })
                                ->columnSpan(['default' => 12, 'md' => 6]),

                            Select::make('claim_type')
                                ->label('Tipo de Sinistro')
                                ->options(ClaimTypeEnum::options())
                                ->searchable()
                                ->required()
                                ->columnSpan(['default' => 12, 'md' => 4]),

                            TextInput::make('protocol_number')
                                ->label('Protocolo da Seguradora')
                                ->placeholder('Ex: PROT-2026-987654')
                                ->maxLength(255)
                                ->columnSpan(['default' => 12, 'md' => 4]),

                            TextInput::make('claim_number')
                                ->label('Nº do Sinistro Interno')
                                ->placeholder('Ex: SIN-2026-001')
                                ->maxLength(255)
                                ->columnSpan(['default' => 12, 'md' => 4]),

                            Select::make('status')
                                ->label('Status do Processo')
                                ->options(ClaimStatusEnum::options())
                                ->default(ClaimStatusEnum::Reported->value)
                                ->required()
                                ->columnSpan(['default' => 12, 'md' => 4]),

                            DateTimePicker::make('occurrence_date')
                                ->label('Data e Hora do Evento')
                                ->required()
                                ->native(false)
                                ->displayFormat('d/m/Y H:i')
                                ->rules([
                                    fn ($get) => function (string $attribute, $value, \Closure $fail) use ($get) {
                                        if (! $value || ! $get('policy_id')) {
                                            return;
                                        }
                                        $policy = Policy::find($get('policy_id'));
                                        if (! $policy) {
                                            return;
                                        }

                                        try {
                                            $occDate = \Carbon\Carbon::parse($value);

                                            if ($policy->start_date && $occDate->lt($policy->start_date)) {
                                                $fail("A data do evento ({$occDate->format('d/m/Y')}) não pode ser anterior ao início da vigência da apólice ({$policy->start_date->format('d/m/Y')}).");
                                            }

                                            if ($policy->end_date && $occDate->gt($policy->end_date)) {
                                                $fail("A data do evento ({$occDate->format('d/m/Y')}) não pode ser posterior ao término da vigência da apólice ({$policy->end_date->format('d/m/Y')}).");
                                            }
                                        } catch (\Throwable) {
                                            // Ignora erro de parsing
                                        }
                                    },
                                ])
                                ->columnSpan(['default' => 12, 'md' => 4]),

                            DateTimePicker::make('report_date')
                                ->label('Data do Aviso')
                                ->default(now())
                                ->required()
                                ->native(false)
                                ->displayFormat('d/m/Y H:i')
                                ->columnSpan(['default' => 12, 'md' => 4]),

                            TextInput::make('location')
                                ->label('Endereço / Local da Ocorrência')
                                ->placeholder('Ex: Av. das Américas, 5000 - Barra da Tijuca, Rio de Janeiro / RJ')
                                ->columnSpanFull(),
                        ])
                        ->columns(12)
                        ->columnSpan(12),

                    // 2. Descrição Detalhada
                    Section::make('Detalhes da Ocorrência')
                        ->extraAttributes(['class' => 'relative z-20 overflow-visible'])
                        ->schema([
                            Textarea::make('occurrence_description')
                                ->label('Descrição Detalhada do Ocorrido')
                                ->placeholder('Descreva detalhadamente a dinâmica dos fatos, danos causados, terceiros ou órgãos de apoio envolvidos...')
                                ->required()
                                ->rows(4)
                                ->columnSpanFull(),
                        ])
                        ->columnSpan(12),

                    // 3. Valores e Franquias
                    Section::make('Regulação Financeira e Indenização')
                        ->extraAttributes(['class' => 'relative z-10 overflow-visible'])
                        ->schema([
                            TextInput::make('estimated_amount')
                                ->label('Prejuízo Estimado')
                                ->prefix('R$')
                                ->placeholder('0,00')
                                ->default('0,00')
                                ->extraInputAttributes([
                                    'x-mask:dynamic' => '$money($input, \',\', \'.\', 2)',
                                ])
                                ->columnSpan(['default' => 12, 'md' => 4]),

                            TextInput::make('deductible_amount')
                                ->label('Valor da Franquia')
                                ->prefix('R$')
                                ->placeholder('0,00')
                                ->default('0,00')
                                ->extraInputAttributes([
                                    'x-mask:dynamic' => '$money($input, \',\', \'.\', 2)',
                                ])
                                ->columnSpan(['default' => 12, 'md' => 4]),

                            TextInput::make('indemnified_amount')
                                ->label('Valor Indenizado Pago')
                                ->prefix('R$')
                                ->placeholder('0,00')
                                ->default('0,00')
                                ->extraInputAttributes([
                                    'x-mask:dynamic' => '$money($input, \',\', \'.\', 2)',
                                ])
                                ->columnSpan(['default' => 12, 'md' => 4]),
                        ])
                        ->columns(12)
                        ->columnSpan(12),

                    // 4. Observações Internas
                    Section::make('Anotações do Atendimento')
                        ->extraAttributes(['class' => 'relative z-1 overflow-visible'])
                        ->schema([
                            Textarea::make('notes')
                                ->label('Observações Internas')
                                ->placeholder('Anotações da regulação, contato com o perito, notas da seguradora...')
                                ->rows(3)
                                ->columnSpanFull(),
                        ])
                        ->collapsible()
                        ->columnSpan(12),
                ]),
        ];
    }

    public static function create(array $data): Claim
    {
        return DB::transaction(function () use ($data) {
            $tenant = Tenant::firstOrCreate(
                ['id' => 1],
                [
                    'name' => 'Empresa Padrão',
                    'slug' => 'empresa-padrao',
                    'email' => 'contato@empresa.com',
                    'document' => '00000000000191',
                ]
            );

            $data['tenant_id'] = auth()->user()?->tenant_id ?? $tenant->id;
            $data['created_by'] = auth()->id();

            return Claim::create($data);
        });
    }
}