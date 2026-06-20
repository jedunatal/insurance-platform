<?php

namespace App\DTOs;

use App\Enums\LeadSourceEnum;
use App\Enums\LeadStatusEnum;
use Carbon\CarbonImmutable;

final readonly class LeadDTO
{
    public function __construct(
        public string $name,
        public LeadSourceEnum $source,
        public LeadStatusEnum $status,
        public int $tenantId,
        public int $createdBy,
        public ?string $email = null,
        public ?string $phone = null,
        public ?int $assignedTo = null,
        public ?CarbonImmutable $nextContactAt = null,
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

            source: LeadSourceEnum::from(
                $data['source']
            ),

            status: LeadStatusEnum::from(
                $data['status']
                ?? LeadStatusEnum::New->value
            ),

            tenantId: (int) $data['tenant_id'],

            createdBy: (int) $data['created_by'],

            email: filled($data['email'] ?? null)
                ? (string) $data['email']
                : null,

            phone: filled($data['phone'] ?? null)
                ? (string) $data['phone']
                : null,

            assignedTo: filled($data['assigned_to'] ?? null)
                ? (int) $data['assigned_to']
                : null,

            nextContactAt: filled($data['next_contact_at'] ?? null)
                ? CarbonImmutable::parse(
                    $data['next_contact_at']
                )
                : null,

            notes: filled($data['notes'] ?? null)
                ? (string) $data['notes']
                : null,
        );
    }

    /**
     * @return array<string,mixed>
     */
    public function toArray(): array
    {
        return [

            'tenant_id' => $this->tenantId,

            'assigned_to' => $this->assignedTo,

            'created_by' => $this->createdBy,

            'name' => $this->name,

            'email' => $this->email,

            'phone' => $this->phone,

            'source' => $this->source->value,

            'status' => $this->status->value,

            'next_contact_at' => $this->nextContactAt?->toDateTimeString(),

            'notes' => $this->notes,

        ];
    }
}