<?php

namespace App\Livewire\Policy;

use App\DTOs\PolicyData;
use App\Enums\PolicyPaymentMethodEnum;
use App\Enums\PolicyStatusEnum;
use App\Models\Insured;
use App\Models\Policy;
use App\Models\Product;
use App\Services\Insurance\PolicyService;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
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
            Grid::make(2)
                ->schema([
                    Select::make('insured_id')
                        ->label('Segurado')
                        ->options(fn () => Insured::query()->pluck('name', 'id'))
                        ->searchable()
                        ->preload()
                        ->required(),

                    Select::make('product_id')
                        ->label('Produto / Ramo')
                        ->options(fn () => Product::query()->pluck('name', 'id'))
                        ->searchable()
                        ->preload()
                        ->nullable(),

                    TextInput::make('policy_number')
                        ->label('Número da Apólice')
                        ->placeholder('Ex: 123456789')
                        ->required(),

                    TextInput::make('proposal_number')
                        ->label('Número da Proposta')
                        ->placeholder('Ex: PROP-000001')
                        ->nullable(),

                    TextInput::make('branch_code')
                        ->label('Código do Ramo (SUSEP)')
                        ->placeholder('Ex: 171')
                        ->maxLength(6)
                        ->nullable(),

                    Select::make('status')
                        ->label('Status')
                        ->options(
                            collect(PolicyStatusEnum::cases())
                                ->pluck('name', 'value')
                                ->toArray()
                        )
                        ->default(PolicyStatusEnum::Active->value)
                        ->required(),

                    DatePicker::make('start_date')
                        ->label('Início da Vigência')
                        ->native(false)
                        ->required(),

                    DatePicker::make('end_date')
                        ->label('Fim da Vigência')
                        ->native(false)
                        ->required(),

                    TextInput::make('net_premium')
                        ->label('Prêmio Líquido')
                        ->numeric()
                        ->prefix('R$')
                        ->default(0)
                        ->required(),

                    TextInput::make('iof_amount')
                        ->label('IOF')
                        ->numeric()
                        ->prefix('R$')
                        ->default(0),

                    TextInput::make('total_premium')
                        ->label('Prêmio Total')
                        ->numeric()
                        ->prefix('R$')
                        ->default(0)
                        ->required(),

                    Select::make('payment_method')
                        ->label('Forma de Pagamento')
                        ->options(
                            collect(PolicyPaymentMethodEnum::cases())
                                ->pluck('name', 'value')
                                ->toArray()
                        )
                        ->required(),

                    TextInput::make('installments_count')
                        ->label('Quantidade de Parcelas')
                        ->numeric()
                        ->default(1)
                        ->required(),

                    Textarea::make('notes')
                        ->label('Observações')
                        ->placeholder('Digite observações sobre a apólice...')
                        ->rows(4)
                        ->columnSpanFull()
                        ->nullable(),
                ]),
        ];
    }

    public static function create(array $data): ?Policy
    {
        return DB::transaction(function () use ($data) {

            /*
            |--------------------------------------------------------------------------
            | TEMPORÁRIO - REMOVER APÓS IMPLEMENTAR AUTENTICAÇÃO
            |--------------------------------------------------------------------------
            | Código original:
            |
            | $data['tenant_id'] = auth()->user()->tenant_id;
            | $data['created_by'] = auth()->id();
            |
            */

            $data['tenant_id'] = null;
            $data['created_by'] = null;

            $dto = PolicyData::fromArray($data);

            return app(PolicyService::class)->create($dto);
        });
    }
}