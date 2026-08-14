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
}