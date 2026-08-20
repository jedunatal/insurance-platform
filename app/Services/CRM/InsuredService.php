<?php

namespace App\Services\CRM;

use App\Actions\Insured\ConvertLeadToInsuredAction;
use App\Actions\Insured\CreateInsuredAction;
use App\Actions\Insured\DeleteInsuredAction;
use App\Actions\Insured\UpdateInsuredAction;
use App\DTOs\InsuredDTO;
use App\Enums\PersonTypeEnum;
use App\Models\Insured;
use App\Models\Lead;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\ModelNotFoundException;

final class InsuredService
{
    public function __construct(
        private readonly CreateInsuredAction $createAction,
        private readonly UpdateInsuredAction $updateAction,
        private readonly DeleteInsuredAction $deleteAction,
        private readonly ConvertLeadToInsuredAction $convertLeadAction,
    ) {
    }

    public function convertLead(Lead|int $lead, array $additionalData = []): Insured
    {
        return $this->convertLeadAction->execute($lead, $additionalData);
    }

    public function create(InsuredDTO $dto): Insured
    {
        return $this->createAction->execute($dto);
    }

    public function update(Insured $insured, InsuredDTO $dto): Insured
    {
        return $this->updateAction->execute($insured, $dto);
    }

    public function delete(Insured $insured): void
    {
        $this->deleteAction->execute($insured);
    }

    /**
     * Wrapper para uso no Livewire (ListAll).
     */
    public function paginate(
        ?string $search = null,
        ?PersonTypeEnum $personType = null,
        ?int $tenantId = null,
        int $perPage = 15
    ): LengthAwarePaginator {
        $tenantId ??= $this->resolveTenantId();

        return $this->list(
            tenantId: $tenantId,
            search: $search,
            personType: $personType,
            perPage: $perPage
        );
    }

    public function list(
        int $tenantId,
        ?string $search = null,
        ?PersonTypeEnum $personType = null,
        ?int $assignedTo = null,
        int $perPage = 15,
    ): LengthAwarePaginator {
        return Insured::query()
            ->forTenant($tenantId)
            ->when($search, fn ($q) => $q->search($search))
            ->when($personType, fn ($q) => $q->byPersonType($personType))
            ->when($assignedTo, fn ($q) => $q->byConsultant($assignedTo))
            ->with([
                'tenant:id,name',
                'assignedTo:id,name',
                'createdBy:id,name',
            ])
            ->latest()
            ->paginate($perPage);
    }

    public function findForTenant(int $id, int $tenantId): Insured
    {
        return Insured::query()
            ->forTenant($tenantId)
            ->with([
                'tenant:id,name',
                'lead:id,name,email',
                'assignedTo:id,name',
                'createdBy:id,name',
            ])
            ->findOrFail($id);
    }

    public function countByPersonType(int $tenantId): array
    {
        $counts = Insured::query()
            ->forTenant($tenantId)
            ->selectRaw('person_type, COUNT(*) as total')
            ->groupBy('person_type')
            ->pluck('total', 'person_type')
            ->all();

        return array_merge(
            array_fill_keys(
                array_map(fn (PersonTypeEnum $p) => $p->value, PersonTypeEnum::cases()),
                0
            ),
            $counts
        );
    }

    public function countActive(int $tenantId): int
    {
        return Insured::query()
            ->forTenant($tenantId)
            ->count();
    }

    public function countToday(int $tenantId): int
    {
        return Insured::query()
            ->forTenant($tenantId)
            ->createdToday()
            ->count();
    }

    /**
     * @throws ModelNotFoundException|RuntimeException
     */
    private function resolveTenantId(): int
    {
        $tenantId = auth()->user()?->tenant_id;

        if ($tenantId === null) {
            throw new \RuntimeException('Não foi possível resolver o tenant do usuário autenticado.');
        }

        return $tenantId;
    }
}
