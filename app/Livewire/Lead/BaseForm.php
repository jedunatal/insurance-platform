<?php

namespace App\Livewire\Lead;

use App\Enums\InsuranceBranchEnum;
use App\Enums\LeadSourceEnum;
use App\Enums\LeadStatusEnum;
use App\Models\Lead;
use App\Models\Product;
use App\Models\Tenant;
use Filament\Forms\Components\DateTimePicker;
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
                    TextInput::make('name')
                        ->label('Nome do Cliente')
                        ->placeholder('Ex: João Bastos da Silva')
                        ->required()
                        ->columnSpanFull(),

                    TextInput::make('email')
                        ->label('E-mail')
                        ->email()
                        ->placeholder('nome@email.com')
                        ->required(),

                    TextInput::make('phone')
                        ->label('Telefone / WhatsApp')
                        ->mask('(99) 99999-9999')
                        ->placeholder('(21) 99999-9999')
                        ->required(),

                    Select::make('person_type')
                        ->label('Tipo de Pessoa')
                        ->options([
                            'PF' => 'Pessoa Física (CPF)',
                            'PJ' => 'Pessoa Jurídica (CNPJ)',
                        ])
                        ->default('PF')
                        ->live()
                        ->dehydrated(false),

                    TextInput::make('document')
                        ->label(fn ($get) => $get('person_type') === 'PJ' ? 'CNPJ' : 'CPF')
                        ->placeholder(fn ($get) => $get('person_type') === 'PJ' ? '00.000.000/0000-00' : '000.000.000-00')
                        ->mask(fn ($get) => $get('person_type') === 'PJ' ? '99.999.999/9999-99' : '999.999.999-99')
                        ->nullable(),

                    Select::make('product_id')
                        ->label('Ramo / Produto de Interesse')
                        ->placeholder('Selecione o produto ou ramo de interesse...')
                        ->options(function () {
                            if (! class_exists(Product::class)) {
                                return InsuranceBranchEnum::options();
                            }

                            $products = Product::query()
                                ->where('is_active', true)
                                ->orderBy('name')
                                ->pluck('name', 'id')
                                ->all();

                            // Fallback caso a tabela ainda não possua produtos cadastrados
                            return ! empty($products) ? $products : InsuranceBranchEnum::options();
                        })
                        ->searchable()
                        ->preload()
                        ->nullable(),

                    Select::make('source')
                        ->label('Origem do Cliente')
                        ->options(LeadSourceEnum::options())
                        ->default('manual')
                        ->required(),

                    Select::make('assigned_to')
                        ->label('Corretor / Consultor Responsável')
                        ->options(fn () => \App\Models\User::where('tenant_id', auth()->user()?->tenant_id ?? 1)->where('is_active', true)->pluck('name', 'id'))
                        ->default(fn () => auth()->id())
                        ->searchable()
                        ->preload()
                        ->nullable(),

                    Select::make('status')
                        ->label('Status Inicial')
                        ->options(LeadStatusEnum::options())
                        ->default(LeadStatusEnum::New->value)
                        ->required(),

                    DateTimePicker::make('next_contact_at')
                        ->label('Agendar Próximo Contato')
                        ->displayFormat('d/m/Y H:i')
                        ->seconds(false)
                        ->nullable()
                        ->columnSpanFull(),

                    Textarea::make('notes')
                        ->label('Notas / Observações da Negociação')
                        ->placeholder('Anote aqui detalhes da negociação...')
                        ->rows(3)
                        ->columnSpanFull()
                        ->nullable(),
                ]),
        ];
    }

    public static function create(array $data): ?Lead
    {
        return DB::transaction(function () use ($data) {
            $tenant = Tenant::firstOrCreate(
                ['id' => 1],
                [
                    'name'     => 'Empresa Padrão',
                    'slug'     => 'empresa-padrao',
                    'email'    => 'contato@empresa.com',
                    'document' => '00000000000191',
                ]
            );

            $data['tenant_id']  = auth()->check() ? (auth()->user()->tenant_id ?? $tenant->id) : $tenant->id;
            $data['created_by'] = auth()->id();

            return Lead::create($data);
        });
    }
}