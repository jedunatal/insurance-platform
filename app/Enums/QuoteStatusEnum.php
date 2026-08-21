<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum QuoteStatusEnum: string implements HasLabel, HasColor, HasIcon
{
    case Draft     = 'draft';
    case Sent      = 'sent';
    case Approved  = 'approved';
    case Converted = 'converted';
    case Rejected  = 'rejected';
    case Expired   = 'expired';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Draft     => 'Rascunho',
            self::Sent      => 'Enviada ao Cliente',
            self::Approved  => 'Aprovada',
            self::Converted => 'Convertida em Apólice',
            self::Rejected  => 'Recusada',
            self::Expired   => 'Expirada',
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::Draft     => 'gray',
            self::Sent      => 'info',
            self::Approved  => 'warning',
            self::Converted => 'success',
            self::Rejected  => 'danger',
            self::Expired   => 'secondary',
        };
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::Draft     => 'heroicon-o-pencil-square',
            self::Sent      => 'heroicon-o-paper-airplane',
            self::Approved  => 'heroicon-o-hand-thumb-up',
            self::Converted => 'heroicon-o-check-badge',
            self::Rejected  => 'heroicon-o-x-circle',
            self::Expired   => 'heroicon-o-clock',
        };
    }

    /**
     * @return array<string, string>
     */
    public static function options(): array
    {
        return array_column(
            array_map(
                fn (self $case) => ['value' => $case->value, 'label' => $case->getLabel()],
                self::cases()
            ),
            'label',
            'value'
        );
    }
}
