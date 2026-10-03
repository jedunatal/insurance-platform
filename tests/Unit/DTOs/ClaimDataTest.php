<?php

namespace Tests\Unit\DTOs;

use App\DTOs\ClaimData;
use App\Enums\ClaimStatusEnum;
use App\Enums\ClaimTypeEnum;
use PHPUnit\Framework\TestCase;

class ClaimDataTest extends TestCase
{
    public function test_from_array_with_complete_data(): void
    {
        $data = [
            'tenant_id' => 1,
            'policy_id' => 10,
            'insured_id' => 20,
            'created_by' => 5,
            'claim_number' => 'SIN-2026-0099',
            'protocol_number' => 'PROT-123456',
            'insurer_claim_number' => 'INS-CLAIM-99',
            'claim_type' => 'collision',
            'status' => 'under_analysis',
            'occurrence_date' => '2026-09-01 10:00:00',
            'report_date' => '2026-09-01 11:30:00',
            'estimated_amount' => 5000.00,
            'indemnified_amount' => 0.00,
            'deductible_amount' => 1500.00,
            'occurrence_description' => 'Colisão frontal.',
            'location' => 'Rua das Flores, 123',
            'third_party_details' => ['driver' => 'João', 'car' => 'Gol'],
            'notes' => 'Aguardando vistoria.',
        ];

        $dto = ClaimData::fromArray($data);

        $this->assertEquals(1, $dto->tenantId);
        $this->assertEquals(10, $dto->policyId);
        $this->assertEquals(20, $dto->insuredId);
        $this->assertEquals('SIN-2026-0099', $dto->claimNumber);
        $this->assertEquals('PROT-123456', $dto->protocolNumber);
        $this->assertSame(ClaimTypeEnum::Collision, $dto->claimType);
        $this->assertSame(ClaimStatusEnum::UnderAnalysis, $dto->status);
        $this->assertEquals(5000.00, $dto->estimatedAmount);
        $this->assertEquals(1500.00, $dto->deductibleAmount);
        $this->assertEquals('Colisão frontal.', $dto->occurrenceDescription);
        $this->assertIsArray($dto->thirdPartyDetails);
        $this->assertEquals('João', $dto->thirdPartyDetails['driver']);
    }

    public function test_from_array_with_fallbacks(): void
    {
        $data = [
            'tenant_id' => 2,
            'policy_id' => 15,
            'insured_id' => 30,
            'occurrence_date' => '2026-09-05 18:00:00',
            'description' => 'Vidro dianteiro trincado por pedra.',
            'estimated_loss' => 450.00,
        ];

        $dto = ClaimData::fromArray($data);

        $this->assertEquals(2, $dto->tenantId);
        $this->assertSame(ClaimStatusEnum::Reported, $dto->status);
        $this->assertNull($dto->claimType);
        $this->assertEquals(450.00, $dto->estimatedAmount);
        $this->assertEquals('Vidro dianteiro trincado por pedra.', $dto->occurrenceDescription);
    }

    public function test_to_array_returns_correct_representation(): void
    {
        $data = [
            'tenant_id' => 1,
            'policy_id' => 2,
            'insured_id' => 3,
            'claim_type' => 'theft',
            'status' => 'reported',
            'occurrence_date' => '2026-10-01 02:00:00',
            'report_date' => '2026-10-01 08:00:00',
            'estimated_amount' => 60000.00,
            'occurrence_description' => 'Veículo furtado na via pública.',
        ];

        $dto = ClaimData::fromArray($data);
        $array = $dto->toArray();

        $this->assertEquals('theft', $array['claim_type']);
        $this->assertEquals('reported', $array['status']);
        $this->assertEquals(60000.00, $array['estimated_amount']);
        $this->assertEquals('Veículo furtado na via pública.', $array['occurrence_description']);
    }
}
