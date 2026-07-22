<?php

namespace App\Livewire\Lead;

use App\Enums\LeadSourceEnum;
use App\Enums\LeadStatusEnum;
use App\Models\Lead;
use App\Models\Product;
use Filament\Actions\Action;
use Filament\Forms;
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
            // 🚀 O Section constrói o card visual idêntico ao do Clipping
            Section::make('Dados do Lead')
                ->description('Preencha as informações do cliente em potencial')
                ->schema([
                    Grid::make(2)
                        ->schema([
                            Forms\Components\TextInput::make('name')
                                ->label('Nome do Lead')
                                ->placeholder('Ex: Carlos Henrique Ramos')
                                ->required()
                                ->columnSpanFull(),

                            Forms\Components\TextInput::make('email')
                                ->label('E-mail')
                                ->email()
                                ->placeholder('carlos@email.com'),

                            Forms\Components\TextInput::make('phone')
                                ->label('Telefone / WhatsApp')
                                ->mask('(99) 99999-9999')
                                ->placeholder('(21) 99999-9999'),

                            Forms\Components\TextInput::make('document')
                                ->label('CPF / CNPJ')
                                ->placeholder('Apenas números'),

                            Forms\Components\Select::make('product_id')
                                ->label('Ramo / Produto de Interesse')
                                ->options(function () {
                                    if (! class_exists(Product::class)) return [];
                                    return Product::query()->where('is_active', true)->pluck('name', 'id');
                                })
                                ->searchable()
                                ->preload()
                                ->nullable(),

                            Forms\Components\Select::make('source')
                                ->label('Origem do Lead')
                                ->options(
                                    collect(LeadSourceEnum::cases())->pluck('name', 'value')->toArray()
                                )
                                ->default('manual')
                                ->required(),

                            Forms\Components\Select::make('status')
                                ->label('Status Inicial')
                                ->options(
                                    collect(LeadStatusEnum::cases())->pluck('name', 'value')->toArray()
                                )
                                ->default('novo')
                                ->required(),

                            Forms\Components\DateTimePicker::make('next_contact_at')
                                ->label('Agendar Próximo Contato')
                                ->displayFormat('d/m/Y H:i')
                                ->seconds(false)
                                ->nullable()
                                ->columnSpanFull(),

                            Forms\Components\Textarea::make('notes')
                                ->label('Notas / Observações da Negociação')
                                ->placeholder('Anote aqui detalhes do veículo, coberturas solicitadas...')
                                ->rows(3)
                                ->columnSpanFull()
                                ->nullable(),
                        ]),
                ]),

            Action::make('save')
                ->label('Salvar Lead')
                ->button()
                ->action('save'),
        ];
    }

    public static function create(array $data): ?Lead
    {
        return DB::transaction(function () use ($data) {
            $data['tenant_id'] = auth()->user()->tenant_id ?? 1;
            $data['created_by'] = auth()->id();

            return Lead::create($data);
        });
    }
}