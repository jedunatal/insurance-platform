<?php

namespace App\Services;

use App\Actions\Lead\CreateLeadAction;
use App\Actions\Lead\DeleteLeadAction;
use App\Actions\Lead\UpdateLeadAction;
use App\DTOs\LeadDTO;
use App\Enums\LeadStatusEnum;
use App\Models\Lead;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\ModelNotFoundException;

final class LeadService
{
    public function __construct(
        private readonly CreateLeadAction $createAction,
        private readonly UpdateLeadAction $updateAction,
        private readonly DeleteLeadAction $deleteAction,
    ) {
    }

    public function create(LeadDTO $dto): Lead
    {
        return $this->createAction->execute($dto);
    }

    public function update(Lead $lead, LeadDTO $dto): Lead
    {
        return $this->updateAction->execute($lead, $dto);
    }

    public function delete(Lead $lead): void
    {
        $this->deleteAction->execute($lead);
    }

    public function list(
        int $tenantId,
        ?string $search = null,
        ?LeadStatusEnum $status = null,
        ?int $assignedTo = null,
        int $perPage = 15,
    ): LengthAwarePaginator {
        return Lead::query()
            ->forTenant($tenantId)
            ->when(
                $search,
                fn ($query) => $query->search($search)
            )
            ->when(
                $status,
                fn ($query) => $query->byStatus($status)
            )
            ->when(
                $assignedTo,
                fn ($query) => $query->byConsultant($assignedTo)
            )
            ->with([
                'tenant:id,name',
                'assignedTo:id,name',
                'createdBy:id,name',
            ])
            ->latest()
            ->paginate($perPage);
    }

    /**
     * @throws ModelNotFoundException
     */
    public function findForTenant(
        int $id,
        int $tenantId
    ): Lead {
        return Lead::query()
            ->forTenant($tenantId)
            ->with([
                'tenant:id,name',
                'assignedTo:id,name',
                'createdBy:id,name',
            ])
            ->findOrFail($id);
    }

    public function countActive(int $tenantId): int
    {
        return Lead::query()
            ->forTenant($tenantId)
            ->active()
            ->count();
    }

    public function countToday(int $tenantId): int
    {
        return Lead::query()
            ->forTenant($tenantId)
            ->createdToday()
            ->count();
    }

    public function countOverdue(int $tenantId): int
    {
        return Lead::query()
            ->forTenant($tenantId)
            ->overdue()
            ->count();
    }

    /**
     * @return array<string, int>
     */
    public function countByStatus(int $tenantId): array
    {
        $counts = Lead::query()
            ->forTenant($tenantId)
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status')
            ->all();

        return array_merge(
            array_fill_keys(
                array_map(
                    fn (LeadStatusEnum $status) => $status->value,
                    LeadStatusEnum::cases()
                ),
                0
            ),
            $counts
        );
    }
}