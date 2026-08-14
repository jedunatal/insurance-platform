<?php

namespace App\Services\Insurance;

use App\Actions\Claim\CreateClaimAction;
use App\Actions\Claim\DeleteClaimAction;
use App\Actions\Claim\UpdateClaimAction;
use App\DTOs\ClaimData;
use App\Models\Claim;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ClaimService
{
    public function __construct(
        protected CreateClaimAction $createAction,
        protected UpdateClaimAction $updateAction,
        protected DeleteClaimAction $deleteAction,
    ) {}

    public function paginateForTenant(int $tenantId, int $perPage = 15): LengthAwarePaginator
    {
        return Claim::where('tenant_id', $tenantId)
            ->with(['insured', 'policy'])
            ->latest()
            ->paginate($perPage);
    }

    public function create(ClaimData $dto): Claim
    {
        return $this->createAction->execute($dto);
    }

    public function update(Claim $claim, ClaimData $dto): Claim
    {
        return $this->updateAction->execute($claim, $dto);
    }

    public function delete(Claim $claim): bool
    {
        return $this->deleteAction->execute($claim);
    }
}