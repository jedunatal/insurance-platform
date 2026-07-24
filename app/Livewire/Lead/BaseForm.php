<?php

namespace App\Livewire\Lead;

use App\Enums\LeadSourceEnum;
use App\Enums\LeadStatusEnum;
use App\Models\Lead;
use App\Models\Product;
use App\Models\Tenant;
use Filament\Actions\Action;
use Filament\Forms;
use Filament\Schemas\Components\Grid;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\DateTimePicker;
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
                        ->placeholder('Ex: Carlos Henrique Ramos')
                        ->required()
                        ->columnSpanFull(),

                    TextInput::make('email')
                        ->label('E-mail')
                        ->email()
                        ->placeholder('carlos@email.com')
                        ->required(),

                    TextInput::make('phone')
                        ->label('Telefone / WhatsApp')
                        ->mask('(99) 99999-9999')
                        ->placeholder('(21) 99999-9999')
                        ->required(),

                    TextInput::make('document')
                        ->label('CPF / CNPJ')
                        ->placeholder('000.000.000-00 ou 00.000.000/0000-00')
                        ->required()
                        ->extraInputAttributes([
                            'x-mask:dynamic' => '$input.length > 14 ? "99.999.999/9999-99" : "999.999.999-99"',
                        ]),

                    Select::make('product_id')
                        ->label('Ramo / Produto de Interesse')
                        ->options(function () {
                            if (!class_exists(Product::class))
                                return [];
                            return Product::query()->where('is_active', true)->pluck('name', 'id');
                        })
                        ->searchable()
                        ->preload()
                        ->nullable(),

                    Select::make('source')
                        ->label('Origem do Cliente')
                        ->options(
                            collect(LeadSourceEnum::cases())->pluck('name', 'value')->toArray()
                        )
                        ->default('manual')
                        ->required(),

                    Select::make('status')
                        ->label('Status Inicial')
                        ->options(
                            collect(LeadStatusEnum::cases())->pluck('name', 'value')->toArray()
                        )
                        ->default('novo')
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
            
            // GARANTIA: Se o Tenant #1 não existir no banco, ele é criado automaticamente aqui
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

            // Grava fisicamente no banco MySQL
            return Lead::create($data);
        });
    }
}