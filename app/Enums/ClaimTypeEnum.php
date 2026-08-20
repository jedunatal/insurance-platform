<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum ClaimTypeEnum: string implements HasLabel, HasColor, HasIcon
{
    case Collision = 'collision';
    case Theft = 'theft';
    case ThirdParty = 'third_party';
    case Fire = 'fire';
    case Electrical = 'electrical';
    case NaturalDisaster = 'natural_disaster';
    case Glass = 'glass';
    case BodyDamage = 'body_damage';
    case Other = 'other';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Collision => 'Colisão',
            self::Theft => 'Roubo / Furto',
            self::ThirdParty => 'Danos a Terceiros',
            self::Fire => 'Incêndio / Queda de Raio',
            self::Electrical => 'Danos Elétricos',
            self::NaturalDisaster => 'Alagamento / Desastres Naturais',
            self::Glass => 'Vidros / Retrovisores / Faróis',
            self::BodyDamage => 'Danos Corporais',
            self::Other => 'Outros',
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::Collision => 'warning',
            self::Theft => 'danger',
            self::ThirdParty => 'info',
            self::Fire => 'danger',
            self::Electrical => 'amber',
            self::NaturalDisaster => 'primary',
            self::Glass => 'gray',
            self::BodyDamage => 'danger',
            self::Other => 'gray',
        };
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::Collision => 'heroicon-o-truck',
            self::Theft => 'heroicon-o-lock-closed',
            self::ThirdParty => 'heroicon-o-user-group',
            self::Fire => 'heroicon-o-fire',
            self::Electrical => 'heroicon-o-bolt',
            self::NaturalDisaster => 'heroicon-o-cloud',
            self::Glass => 'heroicon-o-sparkles',
            self::BodyDamage => 'heroicon-o-heart',
            self::Other => 'heroicon-o-question-mark-circle',
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
