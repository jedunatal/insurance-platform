<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

/**
 * Status do ciclo de vida de uma Apólice.
 *
 * Mapeia o fluxo Porto Seguro:
 * Rascunho -> Vigente -> Renovação Pendente -> Renovada / Cancelada / Expirada.
 */
enum PolicyStatusEnum: string implements HasLabel, HasColor, HasIcon
{
    case Draft = 'draft';
    case Active = 'active';
    case PendingRenewal = 'pending_renewal';
    case Renewed = 'renewed';
    case Cancelled = 'cancelled';
    case Expired = 'expired';

    public function getLabel(): ?string
    {
        return $this->label();
    }

    public function getColor(): string|array|null
    {
        return $this->color();
    }

    public function getIcon(): ?string
    {
        return $this->icon();
    }

    /*
    |--------------------------------------------------------------------------
    | HasLabel
    |--------------------------------------------------------------------------
    */

    public function label(): string
    {
        return match ($this) {
            self::Draft           => 'Rascunho',
            self::Active          => 'Vigente',
            self::PendingRenewal  => 'Renovação Pendente',
            self::Renewed         => 'Renovada',
            self::Cancelled       => 'Cancelada',
            self::Expired         => 'Expirada',
        };
    }

    public function shortLabel(): string
    {
        return match ($this) {
            self::Draft           => 'Rascunho',
            self::Active          => 'Vigente',
            self::PendingRenewal  => 'Renovar',
            self::Renewed         => 'Renovada',
            self::Cancelled       => 'Cancelada',
            self::Expired         => 'Expirada',
        };
    }

    /*
    |--------------------------------------------------------------------------
    | HasColor
    |--------------------------------------------------------------------------
    */

    public function color(): string
    {
        return match ($this) {
            self::Draft           => 'gray',
            self::Active          => 'success',
            self::PendingRenewal  => 'warning',
            self::Renewed         => 'info',
            self::Cancelled       => 'danger',
            self::Expired         => 'danger',
        };
    }

    /**
     * Classes Tailwind para o badge inline (mesmo padrão dos demais Enums).
     */
    public function badgeClasses(): string
    {
        return match ($this) {
            self::Draft           => 'bg-slate-100 text-slate-700 ring-slate-600/20',
            self::Active           => 'bg-green-100 text-green-700 ring-green-600/20',
            self::PendingRenewal  => 'bg-amber-100 text-amber-700 ring-amber-600/20',
            self::Renewed          => 'bg-blue-100 text-blue-700 ring-blue-600/20',
            self::Cancelled       => 'bg-red-100 text-red-700 ring-red-600/20',
            self::Expired         => 'bg-rose-100 text-rose-700 ring-rose-600/20',
        };
    }

    /**
     * Cor literal do "dot" usado em indicadores rápidos.
     */
    public function dotColor(): string
    {
        return match ($this) {
            self::Draft           => 'bg-slate-500',
            self::Active          => 'bg-green-500',
            self::PendingRenewal  => 'bg-amber-500',
            self::Renewed         => 'bg-blue-500',
            self::Cancelled       => 'bg-red-500',
            self::Expired         => 'bg-rose-500',
        };
    }

    /*
    |--------------------------------------------------------------------------
    | HasIcon
    |--------------------------------------------------------------------------
    */

    public function icon(): string
    {
        return match ($this) {
            self::Draft           => 'heroicon-o-document-dashed',
            self::Active          => 'heroicon-o-shield-check',
            self::PendingRenewal  => 'heroicon-o-arrow-path',
            self::Renewed         => 'heroicon-o-arrow-path-rounded-square',
            self::Cancelled       => 'heroicon-o-x-circle',
            self::Expired         => 'heroicon-o-clock',
        };
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers de Estado
    |--------------------------------------------------------------------------
    */

    public function isActive(): bool
    {
        return in_array($this, [
            self::Active,
            self::PendingRenewal,
        ], true);
    }

    public function isEditable(): bool
    {
        return in_array($this, [
            self::Draft,
            self::PendingRenewal,
        ], true);
    }

    public function isDeletable(): bool
    {
        return in_array($this, [
            self::Draft,
            self::Cancelled,
            self::Expired,
        ], true);
    }

    /**
     * Nome do escopo Eloquent correspondente para filtragem rápida.
     */
    public function scopeName(): string
    {
        return match ($this) {
            self::Draft           => 'draft',
            self::Active          => 'active',
            self::PendingRenewal  => 'pendingRenewal',
            self::Renewed         => 'renewed',
            self::Cancelled       => 'cancelled',
            self::Expired         => 'expired',
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
