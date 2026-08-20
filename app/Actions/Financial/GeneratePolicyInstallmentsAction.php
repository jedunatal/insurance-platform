<?php

namespace App\Actions\Financial;

use App\Enums\FinancialStatusEnum;
use App\Models\Policy;
use App\Models\PolicyInstallment;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class GeneratePolicyInstallmentsAction
{
    /**
     * Gera e persiste a grade de parcelas e comissões para a apólice.
     *
     * @return Collection<int, PolicyInstallment>
     */
    public function execute(Policy $policy): Collection
    {
        return DB::transaction(function () use ($policy) {
            // Remove parcelas anteriores caso seja uma re-emissão/recalculo
            $policy->installments()->delete();

            $count = max(1, (int) ($policy->installments_count ?? 1));
            $totalPremium = (float) ($policy->total_premium ?? 0);
            $totalCommission = (float) ($policy->commission_amount ?? 0);

            // Cálculo das parcelas com ajuste fino de centavos na primeira parcela
            $grossBase = floor(($totalPremium / $count) * 100) / 100;
            $grossRemainder = round($totalPremium - ($grossBase * $count), 2);

            $commBase = floor(($totalCommission / $count) * 100) / 100;
            $commRemainder = round($totalCommission - ($commBase * $count), 2);

            $baseDate = $policy->start_date ? Carbon::parse($policy->start_date) : Carbon::today();
            $schedule = [];
            $createdInstallments = collect();

            for ($i = 1; $i <= $count; $i++) {
                // Ajuste de centavos na 1ª parcela
                $grossAmount = ($i === 1) ? round($grossBase + $grossRemainder, 2) : $grossBase;
                $commExpected = ($i === 1) ? round($commBase + $commRemainder, 2) : $commBase;

                // Vencimentos mensais sequenciais
                $dueDate = (clone $baseDate)->addMonthsNoOverflow($i - 1);

                $installment = PolicyInstallment::create([
                    'tenant_id'           => $policy->tenant_id,
                    'policy_id'           => $policy->id,
                    'insured_id'          => $policy->insured_id,
                    'installment_number'  => $i,
                    'total_installments'  => $count,
                    'due_date'            => $dueDate->toDateString(),
                    'gross_amount'        => $grossAmount,
                    'commission_expected' => $commExpected,
                    'commission_received' => null,
                    'status'              => FinancialStatusEnum::Pending,
                ]);

                $createdInstallments->push($installment);

                $schedule[] = [
                    'number'              => $i,
                    'due_date'            => $dueDate->format('Y-m-d'),
                    'gross_amount'        => $grossAmount,
                    'commission_expected' => $commExpected,
                    'status'              => FinancialStatusEnum::Pending->value,
                ];
            }

            // Atualiza o JSON installments_schedule na Apólice para redundância/consulta rápida
            $policy->updateQuietly([
                'installments_schedule' => $schedule,
            ]);

            return $createdInstallments;
        });
    }
}
