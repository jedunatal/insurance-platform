<?php

namespace App\Actions\Policy;

use App\Actions\Financial\GeneratePolicyInstallmentsAction;
use App\DTOs\PolicyData;
use App\Models\Policy;
use Illuminate\Support\Facades\DB;

final class UpdatePolicyAction
{
    public function execute(Policy $policy, PolicyData $dto): Policy
    {
        return DB::transaction(function () use ($policy, $dto) {
            $oldPremium = $policy->total_premium;
            $oldCount = $policy->installments_count;
            $oldCommission = $policy->commission_amount;

            $policy->update($dto->toUpdateArray());
            $refreshed = $policy->fresh();

            if (
                $refreshed->installments()->count() === 0 ||
                (float) $oldPremium !== (float) $refreshed->total_premium ||
                (int) $oldCount !== (int) $refreshed->installments_count ||
                (float) $oldCommission !== (float) $refreshed->commission_amount
            ) {
                app(GeneratePolicyInstallmentsAction::class)->execute($refreshed);
            }

            return $refreshed;
        });
    }
}
