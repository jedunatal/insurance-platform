<?php

namespace App\DTOs;

use App\Enums\ClaimStatusEnum;

readonly class ClaimData
{
    public function __construct(
        public int $tenantId,
        public int $policyId,
        public int $insuredId,
        public ?int $createdBy,
        public ?string $claimNumber,
        public ?string $insurerClaimNumber,
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
        return new self(
            tenantId: $data['tenant_id'],
            policyId: $data['policy_id'],
            insuredId: $data['insured_id'],
            createdBy: $data['created_by'] ?? null,
            claimNumber: $data['claim_number'] ?? null,
            insurerClaimNumber: $data['insurer_claim_number'] ?? null,
            status: $data['status'] instanceof ClaimStatusEnum ? $data['status'] : ClaimStatusEnum::from($data['status']),
            occurrenceDate: $data['occurrence_date'],
            reportDate: $data['report_date'],
            estimatedAmount: (float) ($data['estimated_amount'] ?? 0),
            indemnifiedAmount: (float) ($data['indemnified_amount'] ?? 0),
            deductibleAmount: (float) ($data['deductible_amount'] ?? 0),
            occurrenceDescription: $data['occurrence_description'],
            location: $data['location'] ?? null,
            thirdPartyDetails: $data['third_party_details'] ?? null,
            notes: $data['notes'] ?? null,
        );
    }

    public function toArray(): array
    {
        return [
            'tenant_id' => $this->tenantId,
            'policy_id' => $this->policyId,
            'insured_id' => $this->insuredId,
            'created_by' => $this->createdBy,
            'claim_number' => $this->claimNumber,
            'insurer_claim_number' => $this->insurerClaimNumber,
            'status' => $this->status->value,
            'occurrence_date' => $this->occurrenceDate,
            'report_date' => $this->reportDate,
            'estimated_amount' => $this->estimatedAmount,
            'indemnified_amount' => $this->indemnifiedAmount,
            'deductible_amount' => $this->deductibleAmount,
            'occurrence_description' => $this->occurrenceDescription,
            'location' => $this->location,
            'third_party_details' => $this->thirdPartyDetails,
            'notes' => $this->notes,
        ];
    }
}