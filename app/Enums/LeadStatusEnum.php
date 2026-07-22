<?php

namespace App\Enums;

enum LeadStatusEnum: string
{
    case New = 'Novo';
    case Contact = 'Contato';
    case Proposal = 'Proposta';
    case Converted = 'Convertido';
    case Lost = 'Perdido';

    public function label(): string
    {
        return match ($this) {
            self::New => 'Novo',
            self::Contact => 'Contato',
            self::Proposal => 'Proposta',
            self::Converted => 'Convertido',
            self::Lost => 'Perdido',
        };
    }

    public function badgeClasses(): string
    {
        return match ($this) {
            self::New => 'bg-blue-100 text-blue-700 ring-blue-600/20',
            self::Contact => 'bg-yellow-100 text-yellow-700 ring-yellow-600/20',
            self::Proposal => 'bg-purple-100 text-purple-700 ring-purple-600/20',
            self::Converted => 'bg-green-100 text-green-700 ring-green-600/20',
            self::Lost => 'bg-red-100 text-red-700 ring-red-600/20',
        };
    }

    public function dotColor(): string
    {
        return match ($this) {
            self::New => 'bg-blue-500',
            self::Contact => 'bg-yellow-500',
            self::Proposal => 'bg-purple-500',
            self::Converted => 'bg-green-500',
            self::Lost => 'bg-red-500',
        };
    }

    public function isActive(): bool
    {
        return ! in_array($this, [
            self::Converted,
            self::Lost,
        ], true);
    }

    /**
     * @return array<string, string>
     */
    public static function options(): array
    {
        return array_column(
            array_map(
                fn (self $case) => [
                    'value' => $case->value,
                    'label' => $case->label(),
                ],
                self::cases()
            ),
            'label',
            'value'
        );
    }
}