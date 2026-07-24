<?php

namespace App\Livewire\Insured;

use Filament\Forms;
use Illuminate\Support\Facades\Http;

class BaseForm
{
    public static function schema(): array
    {
        return [
            Forms\Components\Section::make('Informações do Segurado')
                ->schema([
                    Forms\Components\TextInput::make('name')
                        ->label('Nome Completo / Razão Social')
                        ->required()
                        ->maxLength(255),

                    Forms\Components\Select::make('person_type')
                        ->label('Tipo de Pessoa')
                        ->options([
                            'PF' => 'Pessoa Física (CPF)',
                            'PJ' => 'Pessoa Jurídica (CNPJ)',
                        ])
                        ->default('PF')
                        ->required()
                        ->live(),

                    Forms\Components\TextInput::make('document')
                        ->label(fn (Forms\Get $get): string => $get('person_type') === 'PJ' ? 'CNPJ' : 'CPF')
                        ->placeholder(fn (Forms\Get $get): string => $get('person_type') === 'PJ' ? '00.000.000/0000-00' : '000.000.000-00')
                        ->mask(fn (Forms\Get $get): string => $get('person_type') === 'PJ' ? '99.999.999/9999-99' : '999.999.999-99'),

                    Forms\Components\TextInput::make('email')
                        ->label('E-mail')
                        ->email()
                        ->maxLength(255),

                    Forms\Components\TextInput::make('phone')
                        ->label('Telefone / WhatsApp')
                        ->mask('(99) 99999-9999'),

                    Forms\Components\Select::make('assigned_to')
                        ->label('Consultor Responsável')
                        ->relationship('assignedTo', 'name')
                        ->searchable()
                        ->preload(),
                ])->columns(2),

            Forms\Components\Section::make('Endereço Completo')
                ->schema([
                    Forms\Components\TextInput::make('zip_code')
                        ->label('CEP')
                        ->mask('99999-999')
                        ->live(onBlur: true)
                        ->afterStateUpdated(function ($state, Forms\Set $set) {
                            if (blank($state)) return;

                            $cep = preg_replace('/[^0-9]/', '', $state);
                            if (strlen($cep) !== 8) return;

                            $response = Http::get("https://viacep.com.br/ws/{$cep}/json/")->json();

                            if (isset($response['erro'])) return;

                            $set('address', $response['logradouro'] ?? '');
                            $set('neighborhood', $response['bairro'] ?? '');
                            $set('city', $response['localidade'] ?? '');
                            $set('state', $response['uf'] ?? '');
                        }),

                    Forms\Components\TextInput::make('address')
                        ->label('Logradouro / Rua'),

                    Forms\Components\TextInput::make('number')
                        ->label('Número'),

                    Forms\Components\TextInput::make('complement')
                        ->label('Complemento'),

                    Forms\Components\TextInput::make('neighborhood')
                        ->label('Bairro'),

                    Forms\Components\TextInput::make('city')
                        ->label('Cidade'),

                    Forms\Components\TextInput::make('state')
                        ->label('UF')
                        ->maxLength(2),
                ])->columns(3),

            Forms\Components\Section::make('Observações')
                ->schema([
                    Forms\Components\Textarea::make('notes')
                        ->label('Anotações Internas')
                        ->rows(3),
                ]),
        ];
    }
}