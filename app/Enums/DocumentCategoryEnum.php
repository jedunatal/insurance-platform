<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum DocumentCategoryEnum: string implements HasLabel, HasColor, HasIcon
{
    case Cnh              = 'cnh';
    case Rg               = 'rg';
    case ProofOfResidence = 'proof_of_residence';
    case CnpjCard         = 'cnpj_card';
    case SignedProposal   = 'signed_proposal';
    case PolicyDocument   = 'policy_document';
    case PoliceReport     = 'police_report';
    case DamagePhotos     = 'damage_photos';
    case RepairEstimate   = 'repair_estimate';
    case MedicalReport    = 'medical_report';
    case Other            = 'other';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Cnh              => 'CNH / Habilitação',
            self::Rg               => 'RG / Identidade',
            self::ProofOfResidence => 'Comprovante de Residência',
            self::CnpjCard         => 'Cartão CNPJ / Contrato Social',
            self::SignedProposal   => 'Proposta Assinada',
            self::PolicyDocument   => 'Apólice / Condições Gerais',
            self::PoliceReport     => 'Boletim de Ocorrência (BO)',
            self::DamagePhotos     => 'Fotos dos Danos',
            self::RepairEstimate   => 'Orçamento da Oficina / Reparo',
            self::MedicalReport    => 'Laudo Médico / Atestado',
            self::Other            => 'Outros Documentos',
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::Cnh, self::Rg              => 'info',
            self::ProofOfResidence, self::CnpjCard => 'primary',
            self::SignedProposal, self::PolicyDocument => 'success',
            self::PoliceReport, self::DamagePhotos => 'warning',
            self::RepairEstimate, self::MedicalReport => 'amber',
            self::Other                      => 'gray',
        };
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::Cnh, self::Rg              => 'heroicon-o-identification',
            self::ProofOfResidence           => 'heroicon-o-home',
            self::CnpjCard                   => 'heroicon-o-building-office',
            self::SignedProposal             => 'heroicon-o-document-check',
            self::PolicyDocument             => 'heroicon-o-shield-check',
            self::PoliceReport               => 'heroicon-o-shield-exclamation',
            self::DamagePhotos               => 'heroicon-o-camera',
            self::RepairEstimate             => 'heroicon-o-wrench-screwdriver',
            self::MedicalReport              => 'heroicon-o-heart',
            self::Other                      => 'heroicon-o-document',
        };
    }

    public function badgeClasses(): string
    {
        return match ($this) {
            self::Cnh, self::Rg              => 'bg-blue-100 text-blue-700 ring-blue-600/20',
            self::ProofOfResidence           => 'bg-indigo-100 text-indigo-700 ring-indigo-600/20',
            self::CnpjCard                   => 'bg-purple-100 text-purple-700 ring-purple-600/20',
            self::SignedProposal, self::PolicyDocument => 'bg-emerald-100 text-emerald-700 ring-emerald-600/20',
            self::PoliceReport               => 'bg-rose-100 text-rose-700 ring-rose-600/20',
            self::DamagePhotos               => 'bg-amber-100 text-amber-700 ring-amber-600/20',
            self::RepairEstimate             => 'bg-orange-100 text-orange-700 ring-orange-600/20',
            self::MedicalReport              => 'bg-pink-100 text-pink-700 ring-pink-600/20',
            self::Other                      => 'bg-slate-100 text-slate-700 ring-slate-600/20',
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

    public static function fromValue(mixed $value): self
    {
        if ($value instanceof self) {
            return $value;
        }

        $str = strtolower(trim((string) $value));
        $match = match ($str) {
            'cnh', 'habilitacao', 'carteira_motorista' => self::Cnh,
            'rg', 'identidade' => self::Rg,
            'proof_of_residence', 'comprovante_residencia', 'residencia' => self::ProofOfResidence,
            'cnpj_card', 'cnpj', 'contrato_social' => self::CnpjCard,
            'signed_proposal', 'proposta_assinada', 'proposta' => self::SignedProposal,
            'policy_document', 'apolice', 'condicoes_gerais' => self::PolicyDocument,
            'police_report', 'boletim_ocorrencia', 'bo' => self::PoliceReport,
            'damage_photos', 'fotos_danos', 'fotos' => self::DamagePhotos,
            'repair_estimate', 'orcamento', 'oficina' => self::RepairEstimate,
            'medical_report', 'laudo_medico', 'atestado' => self::MedicalReport,
            default => null,
        };

        if ($match) {
            return $match;
        }

        return self::tryFrom((string) $value) ?? self::Other;
    }
}
