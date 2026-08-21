<?php

namespace App\DTOs;

use App\Enums\PersonTypeEnum;

final readonly class InsuredDTO
{
    public function __construct(
        public string $name,
        public int $tenantId,
        public ?int $createdBy = null,
        public ?PersonTypeEnum $personType = null,
        public ?string $birthDate = null,
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
        public ?string $cnhOrRgPath = null,
        public ?string $cpfCnpjDocPath = null,
        public ?string $residenceProofPath = null,
    ) {
    }

    /**
     * @param array<string,mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            name: (string) $data['name'],

            tenantId: (int) ($data['tenant_id'] ?? 1),

            createdBy: self::nullableInt($data['created_by'] ?? null),

            personType: filled($data['person_type'] ?? null)
                ? ($data['person_type'] instanceof PersonTypeEnum ? $data['person_type'] : PersonTypeEnum::from($data['person_type']))
                : PersonTypeEnum::Individual,

            birthDate: self::nullableString($data['birth_date'] ?? null),

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

            cnhOrRgPath: self::nullableString($data['cnh_or_rg_path'] ?? null),

            cpfCnpjDocPath: self::nullableString($data['cpf_cnpj_doc_path'] ?? null),

            residenceProofPath: self::nullableString($data['residence_proof_path'] ?? null),
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

    /**
     * @return array<string,mixed>
     */
    public function toArray(): array
    {
        return [
            'tenant_id'            => $this->tenantId,
            'lead_id'              => $this->leadId,
            'assigned_to'          => $this->assignedTo,
            'created_by'           => $this->createdBy,
            'name'                 => $this->name,
            'email'                => $this->email,
            'phone'                => $this->phone,
            'document'             => $this->document,
            'person_type'          => $this->personType?->value,
            'birth_date'           => $this->birthDate,
            'zip_code'             => $this->zipCode,
            'address'              => $this->address,
            'number'               => $this->number,
            'complement'           => $this->complement,
            'neighborhood'         => $this->neighborhood,
            'city'                 => $this->city,
            'state'                => $this->state,
            'notes'                => $this->notes,
            'cnh_or_rg_path'       => $this->cnhOrRgPath,
            'cpf_cnpj_doc_path'    => $this->cpfCnpjDocPath,
            'residence_proof_path' => $this->residenceProofPath,
        ];
    }
}
