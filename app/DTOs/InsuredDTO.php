<?php

namespace App\DTOs;

use App\Enums\PersonTypeEnum;

final readonly class InsuredDTO
{
    public function __construct(
        public string $name,
        public int $tenantId,
        public int $createdBy,
        public ?PersonTypeEnum $personType = null,
        public ?string $email = null,
        public ?string $phone = null,
        public ?string $document = null,
        public ?int $leadId = null,
        public ?int $assignedTo = null,
        public ?string $zipCode = null,
        public ?string $address = null,
        public ?string $number = null,
        public ?string $complement = null,
        public ?string $neighborhood = null,
        public ?string $city = null,
        public ?string $state = null,
        public ?string $notes = null,
    ) {
    }

    /**
     * @param array<string,mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            name: (string) $data['name'],

            tenantId: (int) $data['tenant_id'],

            createdBy: (int) $data['created_by'],

            personType: filled($data['person_type'] ?? null)
                ? PersonTypeEnum::from($data['person_type'])
                : PersonTypeEnum::Individual,

            email: filled($data['email'] ?? null)
                ? (string) $data['email']
                : null,

            phone: filled($data['phone'] ?? null)
                ? (string) $data['phone']
                : null,

            document: filled($data['document'] ?? null)
                ? (string) $data['document']
                : null,

            leadId: filled($data['lead_id'] ?? null)
                ? (int) $data['lead_id']
                : null,

            assignedTo: filled($data['assigned_to'] ?? null)
                ? (int) $data['assigned_to']
                : null,

            zipCode: self::nullableString($data['zip_code'] ?? null),

            address: self::nullableString($data['address'] ?? null),

            number: self::nullableString($data['number'] ?? null),

            complement: self::nullableString($data['complement'] ?? null),

            neighborhood: self::nullableString($data['neighborhood'] ?? null),

            city: self::nullableString($data['city'] ?? null),

            state: self::nullableString($data['state'] ?? null),

            notes: self::nullableString($data['notes'] ?? null),
        );
    }

    private static function nullableString(mixed $value): ?string
    {
        return filled($value) ? (string) $value : null;
    }

    /**
     * @return array<string,mixed>
     */
    public function toArray(): array
    {
        return [
            'tenant_id'    => $this->tenantId,
            'lead_id'      => $this->leadId,
            'assigned_to'  => $this->assignedTo,
            'created_by'   => $this->createdBy,
            'name'         => $this->name,
            'email'        => $this->email,
            'phone'        => $this->phone,
            'document'     => $this->document,
            'person_type'  => $this->personType?->value,
            'zip_code'     => $this->zipCode,
            'address'      => $this->address,
            'number'       => $this->number,
            'complement'   => $this->complement,
            'neighborhood' => $this->neighborhood,
            'city'         => $this->city,
            'state'        => $this->state,
            'notes'        => $this->notes,
        ];
    }
}
