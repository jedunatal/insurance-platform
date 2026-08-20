<?php

namespace App\DTOs;

use App\Enums\ClaimStatusEnum;
use App\Enums\ClaimTypeEnum;

readonly class ClaimData
{
    public function __construct(
        public int $tenantId,
        public int $policyId,
        public int $insuredId,
        public ?int $createdBy,
        public ?string $claimNumber,
        public ?string $protocolNumber,
        public ?string $insurerClaimNumber,
        public ?ClaimTypeEnum $claimType,
        public ClaimStatusEnum $status,
        public string $occurrenceDate,
        public string $reportDate,
        public float $estimatedAmount,
        public float $indemnifiedAmount,
        public float $deductibleAmount,
        public string $occurrenceDescription,
        public ?string $location,
        public ?array $thirdPartyDetails,
        public ?string $notes,
    ) {}

    public static function fromArray(array $data): self
    {
        $claimType = null;
        if (filled($data['claim_type'] ?? null)) {
            $claimType = $data['claim_type'] instanceof ClaimTypeEnum
                ? $data['claim_type']
                : ClaimTypeEnum::tryFrom($data['claim_type']);
        }

        $protocol = $data['protocol_number'] ?? $data['insurer_claim_number'] ?? null;

        return new self(
            tenantId: (int) ($data['tenant_id'] ?? 1),
            policyId: (int) $data['policy_id'],
            insuredId: (int) $data['insured_id'],
            createdBy: isset($data['created_by']) ? (int) $data['created_by'] : null,
            claimNumber: $data['claim_number'] ?? null,
            protocolNumber: $protocol,
            insurerClaimNumber: $data['insurer_claim_number'] ?? $protocol,
            claimType: $claimType,
            status: ($data['status'] ?? null) instanceof ClaimStatusEnum
                ? $data['status']
                : (filled($data['status'] ?? null) ? ClaimStatusEnum::from($data['status']) : ClaimStatusEnum::Reported),
            occurrenceDate: (string) $data['occurrence_date'],
            reportDate: (string) ($data['report_date'] ?? now()->toDateTimeString()),
            estimatedAmount: (float) ($data['estimated_amount'] ?? $data['estimated_loss'] ?? 0),
            indemnifiedAmount: (float) ($data['indemnified_amount'] ?? $data['indemnity_amount'] ?? 0),
            deductibleAmount: (float) ($data['deductible_amount'] ?? 0),
            occurrenceDescription: (string) ($data['occurrence_description'] ?? $data['description'] ?? ''),
            location: $data['location'] ?? null,
            thirdPartyDetails: $data['third_party_details'] ?? null,
            notes: $data['notes'] ?? null,
        );
    }

    public function toArray(): array
    {
        return [
            'tenant_id'              => $this->tenantId,
            'policy_id'              => $this->policyId,
            'insured_id'             => $this->insuredId,
            'created_by'             => $this->createdBy,
            'claim_number'           => $this->claimNumber,
            'protocol_number'        => $this->protocolNumber,
            'insurer_claim_number'   => $this->insurerClaimNumber,
            'claim_type'             => $this->claimType?->value,
            'status'                 => $this->status->value,
            'occurrence_date'        => $this->occurrenceDate,
            'report_date'            => $this->reportDate,
            'estimated_amount'       => $this->estimatedAmount,
            'indemnified_amount'     => $this->indemnifiedAmount,
            'deductible_amount'      => $this->deductibleAmount,
            'occurrence_description' => $this->occurrenceDescription,
            'location'               => $this->location,
            'third_party_details'    => $this->thirdPartyDetails,
            'notes'                  => $this->notes,
        ];
    }
}