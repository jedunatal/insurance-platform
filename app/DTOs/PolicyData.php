<?php

namespace App\DTOs;

use App\Enums\PolicyPaymentMethodEnum;
use App\Enums\PolicyStatusEnum;
use App\Enums\RenewalStageEnum;
use Carbon\Carbon;

/**
 * Data Transfer Object para Apólices.
 *
 * Carrega os dados validados entre a camada de UI (Livewire),
 * Services e Actions, garantindo tipagem forte e segura.
 */
final readonly class PolicyData
{
    /**
     * @param array<int|string, mixed>|null  $insuredObject
     * @param array<int|string, mixed>|null  $vehicleData
     * @param array<int|string, mixed>|null  $propertyData
     * @param array<int|string, mixed>|null  $beneficiaries
     * @param array<int|string, mixed>|null  $coverages
     * @param array<int|string, mixed>|null  $installmentsSchedule
     */
    public function __construct(
        public int $tenantId,
        public ?int $createdBy = null,
        public ?int $insuredId = null,
        public ?int $previousPolicyId = null,
        public ?int $productId = null,
        public ?int $brokerId = null,
        public ?int $producerId = null,
        public ?string $policyNumber = null,
        public ?string $proposalNumber = null,
        public ?string $insurer = null,
        public ?string $branch = null,
        public ?string $branchCode = null,
        public ?string $susepProcess = null,
        public ?string $ciCode = null,
        public PolicyStatusEnum $status = PolicyStatusEnum::Active,
        public RenewalStageEnum $renewalStatus = RenewalStageEnum::ToContact,
        public ?Carbon $startDate = null,
        public ?Carbon $endDate = null,
        public ?array $insuredObject = null,
        public ?array $vehicleData = null,
        public ?array $propertyData = null,
        public ?array $beneficiaries = null,
        public ?array $coverages = null,
        public ?string $netPremium = null,
        public ?string $iofRate = null,
        public ?string $iofAmount = null,
        public ?string $totalPremium = null,
        public ?string $commissionPercentage = null,
        public ?string $commissionAmount = null,
        public ?string $producerCommissionPercentage = null,
        public ?string $producerCommissionAmount = null,
        public ?string $deductibleAmount = null,
        public PolicyPaymentMethodEnum $paymentMethod = PolicyPaymentMethodEnum::Invoice,
        public ?int $installmentsCount = null,
        public ?array $installmentsSchedule = null,
        public ?string $notes = null,
    ) {
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            tenantId: (int) ($data['tenant_id'] ?? 1),
            createdBy: self::nullableInt($data['created_by'] ?? null),

            insuredId: self::nullableInt($data['insured_id'] ?? null),
            previousPolicyId: self::nullableInt($data['previous_policy_id'] ?? null),
            productId: self::nullableInt($data['product_id'] ?? null),
            brokerId: self::nullableInt($data['broker_id'] ?? null),
            producerId: self::nullableInt($data['producer_id'] ?? null),

            policyNumber: self::nullableString($data['policy_number'] ?? null),
            proposalNumber: self::nullableString($data['proposal_number'] ?? null),
            insurer: self::nullableString($data['insurer'] ?? null),
            branch: self::nullableString($data['branch'] ?? null),
            branchCode: self::nullableString($data['branch_code'] ?? null),
            susepProcess: self::nullableString($data['susep_process'] ?? null),
            ciCode: self::nullableString($data['ci_code'] ?? null),

            status: filled($data['status'] ?? null)
                ? PolicyStatusEnum::fromValue($data['status'])
                : PolicyStatusEnum::Active,

            renewalStatus: filled($data['renewal_status'] ?? null)
                ? ($data['renewal_status'] instanceof RenewalStageEnum ? $data['renewal_status'] : (RenewalStageEnum::tryFrom((string) $data['renewal_status']) ?? RenewalStageEnum::ToContact))
                : RenewalStageEnum::ToContact,

            startDate: self::nullableCarbon($data['start_date'] ?? null),
            endDate: self::nullableCarbon($data['end_date'] ?? null),

            insuredObject: self::nullableArray($data['insured_object'] ?? null),
            vehicleData: self::nullableArray($data['vehicle_data'] ?? null),
            propertyData: self::nullableArray($data['property_data'] ?? null),
            beneficiaries: self::nullableArray($data['beneficiaries'] ?? null),
            coverages: self::nullableArray($data['coverages'] ?? null),

            netPremium: self::nullableDecimal($data['net_premium'] ?? null),
            iofRate: self::nullableDecimal($data['iof_rate'] ?? null),
            iofAmount: self::nullableDecimal($data['iof_amount'] ?? null),
            totalPremium: self::nullableDecimal($data['total_premium'] ?? null),
            commissionPercentage: self::nullableDecimal($data['commission_percentage'] ?? null),
            commissionAmount: self::nullableDecimal($data['commission_amount'] ?? null),
            producerCommissionPercentage: self::nullableDecimal($data['producer_commission_percentage'] ?? null),
            producerCommissionAmount: self::nullableDecimal($data['producer_commission_amount'] ?? null),
            deductibleAmount: self::nullableDecimal($data['deductible_amount'] ?? null),

            paymentMethod: filled($data['payment_method'] ?? null)
                ? ($data['payment_method'] instanceof PolicyPaymentMethodEnum ? $data['payment_method'] : (PolicyPaymentMethodEnum::tryFrom((string) $data['payment_method']) ?? PolicyPaymentMethodEnum::Invoice))
                : PolicyPaymentMethodEnum::Invoice,

            installmentsCount: self::nullableInt($data['installments_count'] ?? null),
            installmentsSchedule: self::nullableArray($data['installments_schedule'] ?? null),

            notes: self::nullableString($data['notes'] ?? null),
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'tenant_id'                      => $this->tenantId,
            'insured_id'                     => $this->insuredId,
            'previous_policy_id'             => $this->previousPolicyId,
            'product_id'                     => $this->productId,
            'broker_id'                      => $this->brokerId,
            'producer_id'                    => $this->producerId,
            'created_by'                     => $this->createdBy,
            'policy_number'                  => $this->policyNumber,
            'proposal_number'                => $this->proposalNumber,
            'insurer'                        => $this->insurer,
            'branch'                         => $this->branch,
            'branch_code'                    => $this->branchCode,
            'susep_process'                  => $this->susepProcess,
            'ci_code'                        => $this->ciCode,
            'status'                         => $this->status->value,
            'renewal_status'                 => $this->renewalStatus->value,
            'start_date'                     => $this->startDate?->toDateTimeString(),
            'end_date'                       => $this->endDate?->toDateTimeString(),
            'insured_object'                 => $this->insuredObject,
            'vehicle_data'                   => $this->vehicleData,
            'property_data'                  => $this->propertyData,
            'beneficiaries'                  => $this->beneficiaries,
            'coverages'                      => $this->coverages,
            'net_premium'                    => $this->netPremium !== null ? (float) $this->netPremium : 0.0,
            'iof_rate'                       => $this->iofRate !== null ? (float) $this->iofRate : 7.38,
            'iof_amount'                     => $this->iofAmount !== null ? (float) $this->iofAmount : 0.0,
            'total_premium'                  => $this->totalPremium !== null ? (float) $this->totalPremium : 0.0,
            'commission_percentage'          => $this->commissionPercentage !== null ? (float) $this->commissionPercentage : 0.0,
            'commission_amount'              => $this->commissionAmount !== null ? (float) $this->commissionAmount : 0.0,
            'producer_commission_percentage' => $this->producerCommissionPercentage !== null ? (float) $this->producerCommissionPercentage : 0.0,
            'producer_commission_amount'     => $this->producerCommissionAmount !== null ? (float) $this->producerCommissionAmount : 0.0,
            'deductible_amount'              => $this->deductibleAmount !== null ? (float) $this->deductibleAmount : 0.0,
            'payment_method'                 => $this->paymentMethod->value,
            'installments_count'             => $this->installmentsCount ?? 1,
            'installments_schedule'          => $this->installmentsSchedule,
            'notes'                          => $this->notes,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function toUpdateArray(): array
    {
        return array_filter(
            $this->toArray(),
            static fn (mixed $value): bool => $value !== null && $value !== ''
        );
    }

    private static function nullableString(mixed $value): ?string
    {
        return filled($value) ? (string) $value : null;
    }

    private static function nullableInt(mixed $value): ?int
    {
        return filled($value) ? (int) $value : null;
    }

    private static function nullableDecimal(mixed $value): ?string
    {
        return filled($value)
            ? \App\Support\CurrencyHelper::toDecimalString($value)
            : null;
    }

    private static function nullableArray(mixed $value): ?array
    {
        if (! filled($value)) {
            return null;
        }

        if (is_string($value)) {
            $decoded = json_decode($value, true);

            return is_array($decoded) ? $decoded : null;
        }

        return is_array($value) ? $value : null;
    }

    private static function nullableCarbon(mixed $value): ?Carbon
    {
        if (! filled($value)) {
            return null;
        }

        if ($value instanceof Carbon) {
            return $value;
        }

        try {
            return Carbon::parse($value);
        } catch (\Throwable) {
            return null;
        }
    }
}
