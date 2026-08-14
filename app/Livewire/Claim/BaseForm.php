<?php

namespace App\Livewire\Claim;

use App\Enums\ClaimStatusEnum;
use App\Models\Claim;
use App\Models\Insured;
use App\Models\Policy;
use App\Models\Tenant;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Get;
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
                        ->options(Insured::pluck('name', 'id'))
                        ->searchable()
                        ->required()
                        ->live(),

                    Select::make('policy_id')
                        ->label('Apólice Vinculada')
                        ->options(fn (Get $get) => Policy::when(
                            $get('insured_id'),
                            fn ($query, $insuredId) => $query->where('insured_id', $insuredId)
                        )->pluck('policy_number', 'id'))
                        ->searchable()
                        ->required(),

                    TextInput::make('claim_number')
                        ->label('Nº do Sinistro Interno')
                        ->placeholder('Ex: SIN-2026-001')
                        ->maxLength(255),

                    TextInput::make('insurer_claim_number')
                        ->label('Nº na Seguradora')
                        ->placeholder('Informe o nº do sinistro na seguradora')
                        ->maxLength(255),

                    Select::make('status')
                        ->label('Status do Processo')
                        ->options(ClaimStatusEnum::class)
                        ->default(ClaimStatusEnum::Reported->value)
                        ->required(),

                    DateTimePicker::make('occurrence_date')
                        ->label('Data e Hora do Evento')
                        ->required(),

                    DateTimePicker::make('report_date')
                        ->label('Data do Aviso')
                        ->default(now())
                        ->required(),

                    TextInput::make('location')
                        ->label('Local do Evento')
                        ->placeholder('Ex: Av. Brasil, 1500 - Rio de Janeiro / RJ')
                        ->columnSpanFull(),

                    Textarea::make('occurrence_description')
                        ->label('Descrição do Ocorrido')
                        ->placeholder('Descreva detalhadamente a ocorrência do sinistro...')
                        ->required()
                        ->rows(4)
                        ->columnSpanFull(),

                    TextInput::make('estimated_amount')
                        ->label('Prejuízo Estimado')
                        ->numeric()
                        ->prefix('R$')
                        ->default(0),

                    TextInput::make('deductible_amount')
                        ->label('Valor da Franquia')
                        ->numeric()
                        ->prefix('R$')
                        ->default(0),

                    TextInput::make('indemnified_amount')
                        ->label('Valor Indenizado')
                        ->numeric()
                        ->prefix('R$')
                        ->default(0),

                    Textarea::make('notes')
                        ->label('Observações Internas')
                        ->placeholder('Anotações adicionais do atendimento...')
                        ->rows(3)
                        ->columnSpanFull(),
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