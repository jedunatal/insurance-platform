<?php

namespace App\Enums;

/**
 * Modalidades de pagamento de uma Apólice.
 *
 * Segue a nomenclatura utilizada na bilhetagem Porto Seguro.
 */
enum PolicyPaymentMethodEnum: string
{
    case CreditCard = 'credit_card';
    case Debit     = 'debit';
    case Invoice   = 'invoice';
    case Pix       = 'pix';

    public function label(): string
    {
        return match ($this) {
            self::CreditCard => 'Cartão de Crédito',
            self::Debit      => 'Débito em Conta',
            self::Invoice    => 'Boleto Bancário',
            self::Pix        => 'Pix',
        };
    }

    public function shortLabel(): string
    {
        return match ($this) {
            self::CreditCard => 'Cartão',
            self::Debit      => 'Débito',
            self::Invoice    => 'Boleto',
            self::Pix        => 'Pix',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::CreditCard => 'heroicon-o-credit-card',
            self::Debit      => 'heroicon-o-building-library',
            self::Invoice    => 'heroicon-o-document-text',
            self::Pix        => 'heroicon-o-bolt',
        };
    }

    public function isRecurring(): bool
    {
        return in_array($this, [
            self::CreditCard,
            self::Debit,
        ], true);
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
