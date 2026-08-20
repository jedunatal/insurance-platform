<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum FinancialStatusEnum: string implements HasLabel, HasColor, HasIcon
{
    case Pending  = 'pending';
    case Paid     = 'paid';
    case Overdue  = 'overdue';
    case Canceled = 'canceled';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Pending  => 'Pendente',
            self::Paid     => 'Pago / Liquidado',
            self::Overdue  => 'Em Atraso',
            self::Canceled => 'Cancelado',
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::Pending  => 'warning',
            self::Paid     => 'success',
            self::Overdue  => 'danger',
            self::Canceled => 'gray',
        };
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::Pending  => 'heroicon-o-clock',
            self::Paid     => 'heroicon-o-check-circle',
            self::Overdue  => 'heroicon-o-exclamation-circle',
            self::Canceled => 'heroicon-o-x-circle',
        };
    }

    public function badgeClasses(): string
    {
        return match ($this) {
            self::Pending  => 'bg-amber-100 text-amber-800 ring-amber-600/20 dark:bg-amber-500/10 dark:text-amber-400 dark:ring-amber-500/20',
            self::Paid     => 'bg-emerald-100 text-emerald-800 ring-emerald-600/20 dark:bg-emerald-500/10 dark:text-emerald-400 dark:ring-emerald-500/20',
            self::Overdue  => 'bg-rose-100 text-rose-800 ring-rose-600/20 dark:bg-rose-500/10 dark:text-rose-400 dark:ring-rose-500/20',
            self::Canceled => 'bg-slate-100 text-slate-800 ring-slate-600/20 dark:bg-slate-800 dark:text-slate-400 dark:ring-slate-700',
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
