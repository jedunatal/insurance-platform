<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum RenewalStageEnum: string implements HasLabel, HasColor, HasIcon
{
    case NotStarted   = 'not_started';
    case ToContact    = 'to_contact';
    case InQuotation  = 'in_quotation';
    case ProposalSent = 'proposal_sent';
    case Renewed      = 'renewed';
    case Lost         = 'lost';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::NotStarted   => 'Vigente (Não Iniciada)',
            self::ToContact    => 'A Contatar',
            self::InQuotation  => 'Em Cotação',
            self::ProposalSent => 'Proposta Enviada',
            self::Renewed      => 'Renovada',
            self::Lost         => 'Não Renovada / Perdida',
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::NotStarted   => 'gray',
            self::ToContact    => 'warning',
            self::InQuotation  => 'info',
            self::ProposalSent => 'primary',
            self::Renewed      => 'success',
            self::Lost         => 'danger',
        };
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::NotStarted   => 'heroicon-o-shield-check',
            self::ToContact    => 'heroicon-o-phone-arrow-up-right',
            self::InQuotation  => 'heroicon-o-calculator',
            self::ProposalSent => 'heroicon-o-paper-airplane',
            self::Renewed      => 'heroicon-o-check-circle',
            self::Lost         => 'heroicon-o-x-circle',
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
