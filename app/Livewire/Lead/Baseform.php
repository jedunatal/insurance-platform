<?php

namespace App\Livewire\Lead;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Grid;

class BaseForm
{
    public static function getFields(): array
    {
        return (new self)->fields();
    }

    public static function fields(): array
    {
        return [
            Grid::make(2)
                ->schema([
                    TextInput::make('name')
                        ->label('Nome do Lead')
                        ->required()
                        ->maxLength(255),

                    TextInput::make('email')
                        ->label('E-mail')
                        ->email()
                        ->required()
                        ->maxLength(255),

                    TextInput::make('phone')
                        ->label('Telefone / WhatsApp')
                        ->mask('(99) 99999-9999')
                        ->placeholder('(21) 99999-9999'),

                    Select::make('status')
                        ->label('Status Inicial')
                        ->options([
                            'novo' => 'Novo',
                            'contatado' => 'Em Atendimento',
                            'qualificado' => 'Qualificado',
                        ])
                        ->default('novo')
                        ->required(),
                ])
        ];
    }
}