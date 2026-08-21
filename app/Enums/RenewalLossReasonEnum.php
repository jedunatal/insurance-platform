<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum RenewalLossReasonEnum: string implements HasLabel
{
    case Price           = 'price';
    case AssetSold       = 'asset_sold';
    case Competitor      = 'competitor';
    case Dissatisfaction = 'dissatisfaction';
    case Other           = 'other';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Price           => 'Preço / Custo Elevado',
            self::AssetSold       => 'Venda do Bem / Descontinuidade',
            self::Competitor      => 'Concorrência / Outro Corretor',
            self::Dissatisfaction => 'Insatisfação com Atendimento ou Sinistro',
            self::Other           => 'Outro Motivo',
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
