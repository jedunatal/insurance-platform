<?php

namespace Tests\Unit\DTOs;

use App\DTOs\InsuredDTO;
use App\Enums\PersonTypeEnum;
use PHPUnit\Framework\TestCase;

class InsuredDTOTest extends TestCase
{
    public function test_from_array_with_complete_individual_data(): void
    {
        $data = [
            'tenant_id' => 1,
            'created_by' => 2,
            'assigned_to' => 3,
            'lead_id' => 45,
            'name' => 'Marcos Vinicius',
            'email' => 'marcos@email.com',
            'phone' => '11987654321',
            'document' => '12345678909',
            'person_type' => 'PF',
            'birth_date' => '1990-05-20',
            'zip_code' => '01310-100',
            'address' => 'Av. Paulista',
            'number' => '1000',
            'complement' => 'Apto 101',
            'neighborhood' => 'Bela Vista',
            'city' => 'São Paulo',
            'state' => 'SP',
            'notes' => 'Segurado premium.',
        ];

        $dto = InsuredDTO::fromArray($data);

        $this->assertEquals(1, $dto->tenantId);
        $this->assertEquals(2, $dto->createdBy);
        $this->assertEquals(3, $dto->assignedTo);
        $this->assertEquals(45, $dto->leadId);
        $this->assertEquals('Marcos Vinicius', $dto->name);
        $this->assertEquals('marcos@email.com', $dto->email);
        $this->assertEquals('11987654321', $dto->phone);
        $this->assertEquals('12345678909', $dto->document);
        $this->assertSame(PersonTypeEnum::Individual, $dto->personType);
        $this->assertEquals('1990-05-20', $dto->birthDate);
        $this->assertEquals('01310-100', $dto->zipCode);
        $this->assertEquals('Av. Paulista', $dto->address);
        $this->assertEquals('1000', $dto->number);
        $this->assertEquals('Apto 101', $dto->complement);
        $this->assertEquals('Bela Vista', $dto->neighborhood);
        $this->assertEquals('São Paulo', $dto->city);
        $this->assertEquals('SP', $dto->state);
        $this->assertEquals('Segurado premium.', $dto->notes);
    }

    public function test_from_array_with_legal_person_enum_and_defaults(): void
    {
        $data = [
            'name' => 'Empresa Alfa Ltda',
            'person_type' => PersonTypeEnum::Legal,
            'document' => '12345678000199',
        ];

        $dto = InsuredDTO::fromArray($data);

        $this->assertEquals(1, $dto->tenantId);
        $this->assertEquals('Empresa Alfa Ltda', $dto->name);
        $this->assertSame(PersonTypeEnum::Legal, $dto->personType);
        $this->assertEquals('12345678000199', $dto->document);
        $this->assertNull($dto->createdBy);
        $this->assertNull($dto->leadId);
        $this->assertNull($dto->address);
    }

    public function test_to_array_returns_correct_keys(): void
    {
        $data = [
            'tenant_id' => 2,
            'name' => 'Joana Prado',
            'email' => 'joana@prado.com',
            'person_type' => 'PF',
            'city' => 'Curitiba',
            'state' => 'PR',
        ];

        $dto = InsuredDTO::fromArray($data);
        $array = $dto->toArray();

        $this->assertIsArray($array);
        $this->assertEquals(2, $array['tenant_id']);
        $this->assertEquals('Joana Prado', $array['name']);
        $this->assertEquals('joana@prado.com', $array['email']);
        $this->assertEquals('PF', $array['person_type']);
        $this->assertEquals('Curitiba', $array['city']);
        $this->assertEquals('PR', $array['state']);
        $this->assertNull($array['notes']);
    }
}
