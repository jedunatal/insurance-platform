<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum InsuranceBranchEnum: string implements HasLabel, HasColor, HasIcon
{
    case Auto = 'Automóvel';
    case Life = 'Vida';
    case Home = 'Residencial';
    case Business = 'Empresarial';
    case Health = 'Saúde';
    case Electronics = 'Equipamentos Portáteis';
    case Rural = 'Agrícola / Rural';
    case Liability = 'Responsabilidade Civil';
    case Other = 'Outros';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Auto => 'Automóvel',
            self::Life => 'Vida',
            self::Home => 'Residencial',
            self::Business => 'Empresarial',
            self::Health => 'Saúde',
            self::Electronics => 'Equipamentos Portáteis',
            self::Rural => 'Agrícola / Rural',
            self::Liability => 'Responsabilidade Civil',
            self::Other => 'Outros',
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::Auto => 'info',
            self::Life => 'success',
            self::Home => 'warning',
            self::Business => 'purple',
            self::Health => 'danger',
            self::Electronics => 'primary',
            self::Rural => 'success',
            self::Liability => 'gray',
            self::Other => 'gray',
        };
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::Auto => 'heroicon-o-truck',
            self::Life => 'heroicon-o-heart',
            self::Home => 'heroicon-o-home',
            self::Business => 'heroicon-o-building-office-2',
            self::Health => 'heroicon-o-shield-check',
            self::Electronics => 'heroicon-o-device-phone-mobile',
            self::Rural => 'heroicon-o-globe-americas',
            self::Liability => 'heroicon-o-scale',
            self::Other => 'heroicon-o-tag',
        };
    }

    public function label(): string
    {
        return $this->getLabel() ?? $this->value;
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
                    'label' => $case->getLabel(),
                ],
                self::cases()
            ),
            'label',
            'value'
        );
    }
}
