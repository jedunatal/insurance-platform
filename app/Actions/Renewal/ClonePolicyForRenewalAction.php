<?php

namespace App\Actions\Renewal;

use App\Actions\Financial\GeneratePolicyInstallmentsAction;
use App\Enums\PolicyPaymentMethodEnum;
use App\Enums\PolicyStatusEnum;
use App\Enums\RenewalStageEnum;
use App\Models\Policy;
use App\Models\PolicyRenewal;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ClonePolicyForRenewalAction
{
    /**
     * Clona a apólice anterior, avança a vigência em +1 ano e gera a nova apólice vinculada.
     */
    public function execute(Policy $oldPolicy): Policy
    {
        return DB::transaction(function () use ($oldPolicy) {
            $startDate = $oldPolicy->end_date ? Carbon::parse($oldPolicy->end_date) : now();
            $endDate = (clone $startDate)->addYear();

            $year = $startDate->year;
            $random = strtoupper(substr(uniqid(), -4));
            $newPolicyNumber = "REN-{$year}-{$random}";

            /** @var Policy $newPolicy */
            $newPolicy = Policy::create([
                'tenant_id'                      => $oldPolicy->tenant_id,
                'insured_id'                     => $oldPolicy->insured_id,
                'previous_policy_id'             => $oldPolicy->id,
                'product_id'                     => $oldPolicy->product_id,
                'broker_id'                      => $oldPolicy->broker_id,
                'producer_id'                    => $oldPolicy->producer_id,
                'created_by'                     => auth()->id() ?? $oldPolicy->created_by,
                'policy_number'                  => $newPolicyNumber,
                'proposal_number'                => "PROP-{$newPolicyNumber}",
                'insurer'                        => $oldPolicy->insurer,
                'branch'                         => $oldPolicy->branch,
                'branch_code'                    => $oldPolicy->branch_code,
                'status'                         => PolicyStatusEnum::Active,
                'renewal_status'                 => RenewalStageEnum::Renewed,
                'start_date'                     => $startDate,
                'end_date'                       => $endDate,
                'insured_object'                 => $oldPolicy->insured_object,
                'vehicle_data'                   => $oldPolicy->vehicle_data,
                'property_data'                  => $oldPolicy->property_data,
                'beneficiaries'                  => $oldPolicy->beneficiaries,
                'coverages'                      => $oldPolicy->coverages,
                'net_premium'                    => $oldPolicy->net_premium ?? 0,
                'iof_rate'                       => $oldPolicy->iof_rate ?? 7.38,
                'iof_amount'                     => $oldPolicy->iof_amount ?? 0,
                'total_premium'                  => $oldPolicy->total_premium ?? 0,
                'commission_percentage'          => $oldPolicy->commission_percentage ?? 0,
                'commission_amount'              => $oldPolicy->commission_amount ?? 0,
                'producer_commission_percentage' => $oldPolicy->producer_commission_percentage ?? 0,
                'producer_commission_amount'     => $oldPolicy->producer_commission_amount ?? 0,
                'deductible_amount'              => $oldPolicy->deductible_amount ?? 0,
                'payment_method'                 => $oldPolicy->payment_method ?? PolicyPaymentMethodEnum::Invoice,
                'installments_count'             => $oldPolicy->installments_count ?? 1,
                'notes'                          => "Renovação da apólice anterior #{$oldPolicy->policy_number}.",
            ]);

            // Gera grade de parcelas para a nova apólice
            app(GeneratePolicyInstallmentsAction::class)->execute($newPolicy);

            // Marca a anterior como Renovada
            $oldPolicy->update([
                'status'         => PolicyStatusEnum::Renewed,
                'renewal_status' => RenewalStageEnum::Renewed,
            ]);

            // Atualiza ou cria o registro da renovação
            PolicyRenewal::updateOrCreate(
                ['policy_id' => $oldPolicy->id],
                [
                    'tenant_id'         => $oldPolicy->tenant_id,
                    'insured_id'        => $oldPolicy->insured_id,
                    'renewed_policy_id' => $newPolicy->id,
                    'stage'             => RenewalStageEnum::Renewed,
                    'target_date'       => $oldPolicy->end_date ? $oldPolicy->end_date->toDateString() : now()->toDateString(),
                ]
            );

            return $newPolicy->fresh();
        });
    }
}
