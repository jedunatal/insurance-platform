<?php

namespace Tests\Unit\DTOs;

use App\DTOs\LeadDTO;
use App\Enums\LeadSourceEnum;
use App\Enums\LeadStatusEnum;
use Carbon\CarbonImmutable;
use PHPUnit\Framework\TestCase;

class LeadDTOTest extends TestCase
{
    public function test_from_array_with_complete_data(): void
    {
        $data = [
            'tenant_id' => 10,
            'created_by' => 5,
            'assigned_to' => 2,
            'name' => 'Ana Clara Silva',
            'email' => 'ana@example.com',
            'phone' => '11999998888',
            'source' => 'whatsapp',
            'status' => 'Novo',
            'next_contact_at' => '2026-10-15 14:00:00',
            'notes' => 'Cliente interessada em seguro de vida e automóvel.',
        ];

        $dto = LeadDTO::fromArray($data);

        $this->assertEquals(10, $dto->tenantId);
        $this->assertEquals(5, $dto->createdBy);
        $this->assertEquals(2, $dto->assignedTo);
        $this->assertEquals('Ana Clara Silva', $dto->name);
        $this->assertEquals('ana@example.com', $dto->email);
        $this->assertEquals('11999998888', $dto->phone);
        $this->assertSame(LeadSourceEnum::Whatsapp, $dto->source);
        $this->assertSame(LeadStatusEnum::New, $dto->status);
        $this->assertInstanceOf(CarbonImmutable::class, $dto->nextContactAt);
        $this->assertEquals('2026-10-15 14:00:00', $dto->nextContactAt->format('Y-m-d H:i:s'));
        $this->assertEquals('Cliente interessada em seguro de vida e automóvel.', $dto->notes);
    }

    public function test_from_array_with_minimal_data(): void
    {
        $data = [
            'tenant_id' => 1,
            'created_by' => 1,
            'name' => 'Carlos Souza',
            'source' => 'site',
        ];

        $dto = LeadDTO::fromArray($data);

        $this->assertEquals(1, $dto->tenantId);
        $this->assertEquals(1, $dto->createdBy);
        $this->assertEquals('Carlos Souza', $dto->name);
        $this->assertSame(LeadSourceEnum::Site, $dto->source);
        $this->assertSame(LeadStatusEnum::New, $dto->status);
        $this->assertNull($dto->assignedTo);
        $this->assertNull($dto->email);
        $this->assertNull($dto->phone);
        $this->assertNull($dto->nextContactAt);
        $this->assertNull($dto->notes);
    }

    public function test_to_array_returns_expected_structure(): void
    {
        $data = [
            'tenant_id' => 3,
            'created_by' => 7,
            'assigned_to' => 4,
            'name' => 'Juliana Mendes',
            'email' => 'juliana@empresa.com',
            'phone' => '21988887777',
            'source' => 'referral',
            'status' => 'Em Negociação',
            'next_contact_at' => '2026-11-01 10:30:00',
            'notes' => 'Indicação de cliente antigo.',
        ];

        $dto = LeadDTO::fromArray($data);
        $array = $dto->toArray();

        $this->assertIsArray($array);
        $this->assertEquals(3, $array['tenant_id']);
        $this->assertEquals(7, $array['created_by']);
        $this->assertEquals(4, $array['assigned_to']);
        $this->assertEquals('Juliana Mendes', $array['name']);
        $this->assertEquals('juliana@empresa.com', $array['email']);
        $this->assertEquals('21988887777', $array['phone']);
        $this->assertEquals('referral', $array['source']);
        $this->assertEquals('Em Negociação', $array['status']);
        $this->assertEquals('2026-11-01 10:30:00', $array['next_contact_at']);
        $this->assertEquals('Indicação de cliente antigo.', $array['notes']);
    }
}
