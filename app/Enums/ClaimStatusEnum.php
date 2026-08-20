<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum ClaimStatusEnum: string implements HasLabel, HasColor, HasIcon
{
    case Reported = 'reported';
    case UnderAnalysis = 'under_analysis';
    case Inspection = 'inspection';
    case Approved = 'approved';
    case Indemnified = 'indemnified';
    case Rejected = 'rejected';
    case Cancelled = 'cancelled';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Reported => 'Avisado',
            self::UnderAnalysis => 'Em Análise',
            self::Inspection => 'Vistoria / Orçamento',
            self::Approved => 'Aprovado',
            self::Indemnified => 'Indenizado / Concluído',
            self::Rejected => 'Recusado',
            self::Cancelled => 'Cancelado',
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::Reported => 'info',
            self::UnderAnalysis => 'warning',
            self::Inspection => 'gray',
            self::Approved => 'success',
            self::Indemnified => 'success',
            self::Rejected => 'danger',
            self::Cancelled => 'danger',
        };
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::Reported => 'heroicon-m-megaphone',
            self::UnderAnalysis => 'heroicon-m-magnifying-glass',
            self::Inspection => 'heroicon-m-clipboard-document-check',
            self::Approved => 'heroicon-m-check-circle',
            self::Indemnified => 'heroicon-m-currency-dollar',
            self::Rejected => 'heroicon-m-x-circle',
            self::Cancelled => 'heroicon-m-slash',
        };
    }

    public function label(): string
    {
        return $this->getLabel() ?? $this->value;
    }

    public function badgeClasses(): string
    {
        return match ($this) {
            self::Reported => 'bg-blue-100 text-blue-700 ring-blue-600/20',
            self::UnderAnalysis => 'bg-yellow-100 text-yellow-700 ring-yellow-600/20',
            self::Inspection => 'bg-slate-100 text-slate-700 ring-slate-600/20',
            self::Approved => 'bg-emerald-100 text-emerald-700 ring-emerald-600/20',
            self::Indemnified => 'bg-green-100 text-green-700 ring-green-600/20',
            self::Rejected => 'bg-red-100 text-red-700 ring-red-600/20',
            self::Cancelled => 'bg-rose-100 text-rose-700 ring-rose-600/20',
        };
    }

    public function dotColor(): string
    {
        return match ($this) {
            self::Reported => 'bg-blue-500',
            self::UnderAnalysis => 'bg-yellow-500',
            self::Inspection => 'bg-slate-500',
            self::Approved => 'bg-emerald-500',
            self::Indemnified => 'bg-green-500',
            self::Rejected => 'bg-red-500',
            self::Cancelled => 'bg-rose-500',
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
                    'label' => $case->getLabel(),
                ],
                self::cases()
            ),
            'label',
            'value'
        );
    }
}