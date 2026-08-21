<?php

namespace App\Actions\Renewal;

use App\Enums\RenewalStageEnum;
use App\Models\Policy;
use App\Models\PolicyRenewal;
use Illuminate\Support\Facades\DB;

class StartPolicyRenewalAction
{
    /**
     * Inicia o processo de renovação de uma apólice na esteira de retenção.
     */
    public function execute(Policy $policy, ?int $assignedTo = null, ?string $notes = null): PolicyRenewal
    {
        return DB::transaction(function () use ($policy, $assignedTo, $notes) {
            $policy->update([
                'renewal_status' => RenewalStageEnum::ToContact,
            ]);

            $targetDate = $policy->end_date ? $policy->end_date->toDateString() : now()->addMonth()->toDateString();

            /** @var PolicyRenewal $renewal */
            $renewal = PolicyRenewal::updateOrCreate(
                [
                    'policy_id' => $policy->id,
                ],
                [
                    'tenant_id'   => $policy->tenant_id,
                    'insured_id'  => $policy->insured_id,
                    'assigned_to' => $assignedTo ?? $policy->broker_id,
                    'created_by'  => auth()->id() ?? $policy->created_by,
                    'stage'       => RenewalStageEnum::ToContact,
                    'target_date' => $targetDate,
                    'notes'       => $notes ?? $policy->notes,
                ]
            );

            return $renewal;
        });
    }
}
