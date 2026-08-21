<?php

namespace App\Support;

class CurrencyHelper
{
    /**
     * Converte qualquer representação monetária (BRL, string ou numérico) para float.
     * Exemplos: "R$ 3.250,50", "3.250,50", "1500.50", "1500,50", 1500 => 3250.50 / 1500.50 / 1500.00
     */
    public static function parse(mixed $value): float
    {
        if ($value === null || $value === '') {
            return 0.0;
        }

        if (is_numeric($value)) {
            return (float) $value;
        }

        if (is_string($value)) {
            $cleaned = trim(str_replace(['R$', 'r$', ' ', ' '], '', $value));

            // Se contém ponto como separador de milhar e vírgula como decimal (ex: 1.250,50)
            if (str_contains($cleaned, '.') && str_contains($cleaned, ',')) {
                $cleaned = str_replace('.', '', $cleaned);
                $cleaned = str_replace(',', '.', $cleaned);
            } elseif (str_contains($cleaned, ',')) {
                // Se contém apenas vírgula como decimal (ex: 1250,50)
                $cleaned = str_replace(',', '.', $cleaned);
            }

            // Remove quaisquer outros caracteres que não sejam dígitos, ponto ou sinal negativo
            $cleaned = preg_replace('/[^\d.-]/', '', $cleaned);

            return is_numeric($cleaned) ? (float) $cleaned : 0.0;
        }

        return (float) $value;
    }

    /**
     * Retorna o valor limpo em formato decimal de string com 2 casas (ex: "1500.50").
     */
    public static function toDecimalString(mixed $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        $float = self::parse($value);

        return number_format($float, 2, '.', '');
    }

    /**
     * Formata um valor numérico ou string para o padrão brasileiro BRL (ex: "1.500,50" ou "R$ 1.500,50").
     */
    public static function format(mixed $value, bool $withSymbol = false): string
    {
        $float = self::parse($value);
        $formatted = number_format($float, 2, ',', '.');

        return $withSymbol ? "R$ {$formatted}" : $formatted;
    }
}
