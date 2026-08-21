<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum AttachmentCategoryEnum: string implements HasLabel, HasColor, HasIcon
{
    case Cnh              = 'cnh';
    case Crlv             = 'crlv';
    case Policy           = 'policy';
    case Inspection       = 'inspection';
    case PoliceReport     = 'bo';
    case Photo            = 'photo';
    case Budget           = 'budget';
    case ProofOfResidence = 'proof_of_residence';
    case Other            = 'other';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Cnh              => 'CNH / Identificação',
            self::Crlv             => 'CRLV / Doc. Veículo',
            self::Policy           => 'Apólice / Proposta Seguradora',
            self::Inspection       => 'Laudo / Vistoria Prévia',
            self::PoliceReport     => 'Boletim de Ocorrência (B.O.)',
            self::Photo            => 'Fotos do Bem / Avarias',
            self::Budget           => 'Orçamento / Nota Fiscal',
            self::ProofOfResidence => 'Comprovante de Endereço',
            self::Other            => 'Outro Documento',
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::Cnh              => 'info',
            self::Crlv             => 'primary',
            self::Policy           => 'success',
            self::Inspection       => 'warning',
            self::PoliceReport     => 'danger',
            self::Photo            => 'secondary',
            self::Budget           => 'amber',
            self::ProofOfResidence => 'gray',
            self::Other            => 'gray',
        };
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::Cnh              => 'heroicon-o-identification',
            self::Crlv             => 'heroicon-o-truck',
            self::Policy           => 'heroicon-o-document-text',
            self::Inspection       => 'heroicon-o-clipboard-document-check',
            self::PoliceReport     => 'heroicon-o-shield-exclamation',
            self::Photo            => 'heroicon-o-camera',
            self::Budget           => 'heroicon-o-banknotes',
            self::ProofOfResidence => 'heroicon-o-home',
            self::Other            => 'heroicon-o-paper-clip',
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
