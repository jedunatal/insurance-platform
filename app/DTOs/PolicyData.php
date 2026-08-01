<?php

namespace App\DTOs;

use App\Enums\PolicyPaymentMethodEnum;
use App\Enums\PolicyStatusEnum;
use Carbon\Carbon;

/**
 * Data Transfer Object para Apólices.
 *
 * Carrega os dados validados entre a camada de UI (Livewire),
 * Services e Actions, garantindo tipagem forte e seguro.
 */
final readonly class PolicyData
{
    /**
     * @param array<int|string, mixed>|null  $insuredObject
     * @param array<int|string, mixed>|null  $coverages
     * @param array<int|string, mixed>|null  $installmentsSchedule
     */
    public function __construct(
        public int $tenantId,
        public int $createdBy,
        public ?int $insuredId = null,
        public ?int $productId = null,
        public ?int $brokerId = null,
        public ?string $policyNumber = null,
        public ?string $proposalNumber = null,
        public ?string $branchCode = null,
        public ?string $susepProcess = null,
        public ?string $ciCode = null,
        public PolicyStatusEnum $status = PolicyStatusEnum::Draft,
        public ?Carbon $startDate = null,
        public ?Carbon $endDate = null,
        public ?array $insuredObject = null,
        public ?array $coverages = null,
        public ?string $netPremium = null,
        public ?string $iofAmount = null,
        public ?string $totalPremium = null,
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
            tenantId: (int) $data['tenant_id'],
            createdBy: (int) $data['created_by'],

            insuredId: self::nullableInt($data['insured_id'] ?? null),
            productId: self::nullableInt($data['product_id'] ?? null),
            brokerId: self::nullableInt($data['broker_id'] ?? null),

            policyNumber: self::nullableString($data['policy_number'] ?? null),
            proposalNumber: self::nullableString($data['proposal_number'] ?? null),
            branchCode: self::nullableString($data['branch_code'] ?? null),
            susepProcess: self::nullableString($data['susep_process'] ?? null),
            ciCode: self::nullableString($data['ci_code'] ?? null),

            status: filled($data['status'] ?? null)
                ? PolicyStatusEnum::from((string) $data['status'])
                : PolicyStatusEnum::Draft,

            startDate: self::nullableCarbon($data['start_date'] ?? null),
            endDate: self::nullableCarbon($data['end_date'] ?? null),

            insuredObject: self::nullableArray($data['insured_object'] ?? null),
            coverages: self::nullableArray($data['coverages'] ?? null),

            netPremium: self::nullableDecimal($data['net_premium'] ?? null),
            iofAmount: self::nullableDecimal($data['iof_amount'] ?? null),
            totalPremium: self::nullableDecimal($data['total_premium'] ?? null),

            paymentMethod: filled($data['payment_method'] ?? null)
                ? PolicyPaymentMethodEnum::from((string) $data['payment_method'])
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
            'tenant_id'             => $this->tenantId,
            'insured_id'           => $this->insuredId,
            'product_id'           => $this->productId,
            'broker_id'            => $this->brokerId,
            'created_by'           => $this->createdBy,
            'policy_number'        => $this->policyNumber,
            'proposal_number'      => $this->proposalNumber,
            'branch_code'          => $this->branchCode,
            'susep_process'        => $this->susepProcess,
            'ci_code'              => $this->ciCode,
            'status'               => $this->status->value,
            'start_date'           => $this->startDate?->toDateTimeString(),
            'end_date'             => $this->endDate?->toDateTimeString(),
            'insured_object'       => $this->insuredObject,
            'coverages'            => $this->coverages,
            'net_premium'          => $this->netPremium !== null ? (float) $this->netPremium : null,
            'iof_amount'           => $this->iofAmount !== null ? (float) $this->iofAmount : null,
            'total_premium'        => $this->totalPremium !== null ? (float) $this->totalPremium : null,
            'payment_method'       => $this->paymentMethod->value,
            'installments_count'   => $this->installmentsCount,
            'installments_schedule'=> $this->installmentsSchedule,
            'notes'                => $this->notes,
        ];
    }

    /**
     * Versão para update: remove chaves null para não sobrescrever
     * campos existentes com null acidentalmente.
     *
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
            ? number_format((float) $value, 2, '.', '')
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
