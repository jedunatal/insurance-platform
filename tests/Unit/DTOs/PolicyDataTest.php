<?php

namespace Tests\Unit\DTOs;

use App\DTOs\PolicyData;
use App\Enums\PolicyPaymentMethodEnum;
use App\Enums\PolicyStatusEnum;
use Carbon\Carbon;
use PHPUnit\Framework\TestCase;

class PolicyDataTest extends TestCase
{
    public function test_from_array_with_complete_policy_data(): void
    {
        $coverages = [
            ['name' => 'Colisão e Incêndio', 'limit' => '100% FIPE', 'deductible' => 'R$ 2.500,00'],
            ['name' => 'Danos a Terceiros', 'limit' => 'R$ 100.000,00', 'deductible' => 'Isento'],
        ];

        $data = [
            'tenant_id' => 1,
            'insured_id' => 10,
            'product_id' => 2,
            'broker_id' => 3,
            'created_by' => 1,
            'policy_number' => 'POL-2026-001',
            'proposal_number' => 'PROP-2026-001',
            'insurer' => 'Porto Seguro',
            'branch' => 'Automóvel',
            'branch_code' => '0531',
            'susep_process' => '15414.000001/2026-10',
            'ci_code' => 'CI-998877',
            'status' => 'active',
            'start_date' => '2026-01-01 00:00:00',
            'end_date' => '2027-01-01 00:00:00',
            'net_premium' => 2000.00,
            'iof_rate' => 7.38,
            'iof_amount' => 147.60,
            'total_premium' => 2147.60,
            'commission_percentage' => 10.00,
            'commission_amount' => 200.00,
            'deductible_amount' => 2500.00,
            'payment_method' => 'invoice',
            'installments_count' => 4,
            'coverages' => $coverages,
            'notes' => 'Apólice emitida com sucesso.',
        ];

        $dto = PolicyData::fromArray($data);

        $this->assertEquals(1, $dto->tenantId);
        $this->assertEquals(10, $dto->insuredId);
        $this->assertEquals(2, $dto->productId);
        $this->assertEquals(3, $dto->brokerId);
        $this->assertEquals('POL-2026-001', $dto->policyNumber);
        $this->assertEquals('Porto Seguro', $dto->insurer);
        $this->assertEquals('Automóvel', $dto->branch);
        $this->assertSame(PolicyStatusEnum::Active, $dto->status);
        $this->assertSame(PolicyPaymentMethodEnum::Invoice, $dto->paymentMethod);
        $this->assertInstanceOf(Carbon::class, $dto->startDate);
        $this->assertInstanceOf(Carbon::class, $dto->endDate);
        $this->assertEquals('2000.00', $dto->netPremium);
        $this->assertEquals('147.60', $dto->iofAmount);
        $this->assertEquals('2147.60', $dto->totalPremium);
        $this->assertEquals(4, $dto->installmentsCount);
        $this->assertCount(2, $dto->coverages);
        $this->assertEquals('Apólice emitida com sucesso.', $dto->notes);
    }

    public function test_from_array_with_defaults(): void
    {
        $data = [
            'tenant_id' => 2,
        ];

        $dto = PolicyData::fromArray($data);

        $this->assertEquals(2, $dto->tenantId);
        $this->assertSame(PolicyStatusEnum::Active, $dto->status);
        $this->assertSame(PolicyPaymentMethodEnum::Invoice, $dto->paymentMethod);
        $this->assertNull($dto->policyNumber);
        $this->assertNull($dto->startDate);
    }

    public function test_to_array_and_to_update_array(): void
    {
        $data = [
            'tenant_id' => 1,
            'policy_number' => 'POL-XYZ',
            'net_premium' => 1000.00,
            'notes' => null,
        ];

        $dto = PolicyData::fromArray($data);
        $array = $dto->toArray();

        $this->assertEquals(1, $array['tenant_id']);
        $this->assertEquals('POL-XYZ', $array['policy_number']);
        $this->assertEquals(1000.00, $array['net_premium']);
        $this->assertArrayHasKey('notes', $array);

        $updateArray = $dto->toUpdateArray();
        $this->assertArrayNotHasKey('notes', $updateArray);
        $this->assertEquals('POL-XYZ', $updateArray['policy_number']);
    }
}
