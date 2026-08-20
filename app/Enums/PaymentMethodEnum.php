<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum PaymentMethodEnum: string implements HasLabel, HasColor, HasIcon
{
    case Invoice    = 'invoice';
    case CreditCard = 'credit_card';
    case Debit      = 'debit';
    case Pix        = 'pix';
    case Payroll    = 'payroll';
    case Other      = 'other';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Invoice    => 'Boleto Bancário',
            self::CreditCard => 'Cartão de Crédito',
            self::Debit      => 'Débito em Conta',
            self::Pix        => 'PIX',
            self::Payroll    => 'Desconto em Folha',
            self::Other      => 'Outro',
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::Invoice    => 'info',
            self::CreditCard => 'primary',
            self::Debit      => 'warning',
            self::Pix        => 'success',
            self::Payroll    => 'purple',
            self::Other      => 'gray',
        };
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::Invoice    => 'heroicon-o-document-text',
            self::CreditCard => 'heroicon-o-credit-card',
            self::Debit      => 'heroicon-o-building-library',
            self::Pix        => 'heroicon-o-bolt',
            self::Payroll    => 'heroicon-o-user-group',
            self::Other      => 'heroicon-o-banknotes',
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
