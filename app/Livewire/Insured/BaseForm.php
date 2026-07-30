<?php

namespace App\Livewire\Insured;

use App\Models\Insured;
use App\Models\Tenant;
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

                    Select::make('person_type')
                        ->label('Tipo de Pessoa')
                        ->options([
                            'PF' => 'Pessoa Física',
                            'PJ' => 'Pessoa Jurídica',
                        ])
                        ->default('PF')
                        ->required()
                        ->live(),

                    TextInput::make('document')
                        ->label('CPF / CNPJ')
                        ->placeholder('Informe o CPF ou CNPJ')
                        ->required()
                        ->extraInputAttributes([
                            'x-mask:dynamic' => '$input.length > 14 
                                ? "99.999.999/9999-99" 
                                : "999.999.999-99"',
                        ]),

                    TextInput::make('name')
                        ->label('Nome / Razão Social')
                        ->placeholder('Digite o nome do segurado')
                        ->required()
                        ->maxLength(255)
                        ->columnSpanFull(),

                    TextInput::make('email')
                        ->label('E-mail')
                        ->email()
                        ->placeholder('cliente@email.com'),

                    TextInput::make('phone')
                        ->label('Telefone / WhatsApp')
                        ->placeholder('(21) 99999-9999')
                        ->tel()
                        ->mask('(99) 99999-9999'),

                    TextInput::make('zip_code')
                        ->label('CEP')
                        ->placeholder('00000-000')
                        ->mask('99999-999')
                        ->extraInputAttributes([
                            'x-on:blur' => 'buscarCep($event.target.value)',
                        ]),

                    TextInput::make('address')
                        ->label('Endereço')
                        ->columnSpanFull(),

                    TextInput::make('number')
                        ->label('Número'),

                    TextInput::make('complement')
                        ->label('Complemento'),

                    TextInput::make('neighborhood')
                        ->label('Bairro'),

                    TextInput::make('city')
                        ->label('Cidade'),

                    TextInput::make('state')
                        ->label('UF')
                        ->maxLength(2),

                    Textarea::make('notes')
                        ->label('Observações')
                        ->placeholder('Informações adicionais do segurado...')
                        ->rows(4)
                        ->columnSpanFull(),
                ]),
        ];
    }


    public static function create(array $data): Insured
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

            return Insured::create($data);
        });
    }
}