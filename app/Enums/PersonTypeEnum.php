<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum PersonTypeEnum: string implements HasLabel
{
    case Individual = 'PF';
    case Legal = 'PJ';

    public function getLabel(): ?string
    {
        return $this->label();
    }

    public function label(): string
    {
        return match ($this) {
            self::Individual => 'Pessoa Física',
            self::Legal => 'Pessoa Jurídica',
        };
    }

    public function shortLabel(): string
    {
        return match ($this) {
            self::Individual => 'PF',
            self::Legal => 'PJ',
        };
    }

    public function documentLabel(): string
    {
        return match ($this) {
            self::Individual => 'CPF',
            self::Legal => 'CNPJ',
        };
    }

    public function documentMask(): string
    {
        return match ($this) {
            self::Individual => '999.999.999-99',
            self::Legal => '99.999.999/9999-99',
        };
    }

    public function documentPlaceholder(): string
    {
        return match ($this) {
            self::Individual => '000.000.000-00',
            self::Legal => '00.000.000/0000-00',
        };
    }

    public function badgeClasses(): string
    {
        return match ($this) {
            self::Individual => 'bg-blue-100 text-blue-700 ring-blue-600/20',
            self::Legal => 'bg-amber-100 text-amber-700 ring-amber-600/20',
        };
    }

    public function documentLength(): int
    {
        return match ($this) {
            self::Individual => 11,
            self::Legal => 14,
        };
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
