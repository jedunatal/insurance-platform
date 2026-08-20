<?php

namespace App\Actions\Financial;

use App\Enums\FinancialStatusEnum;
use App\Models\PolicyInstallment;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class SettleInstallmentAction
{
    /**
     * Liquida uma parcela de apólice e registra a comissão recebida.
     */
    public function execute(
        PolicyInstallment $installment,
        Carbon|string|null $paymentDate = null,
        ?float $commissionReceived = null,
        ?string $notes = null
    ): PolicyInstallment {
        return DB::transaction(function () use ($installment, $paymentDate, $commissionReceived, $notes) {
            $parsedDate = $paymentDate
                ? ($paymentDate instanceof Carbon ? $paymentDate : Carbon::parse($paymentDate))
                : Carbon::today();

            $commission = $commissionReceived !== null
                ? $commissionReceived
                : (float) $installment->commission_expected;

            $installment->update([
                'payment_date'        => $parsedDate->toDateString(),
                'commission_received' => $commission,
                'status'              => FinancialStatusEnum::Paid,
                'notes'               => $notes ?? $installment->notes,
            ]);

            return $installment->fresh();
        });
    }
}
