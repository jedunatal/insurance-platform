<?php

namespace App\Actions\Renewal;

use App\Enums\RenewalLossReasonEnum;
use App\Enums\RenewalStageEnum;
use App\Models\PolicyRenewal;
use Illuminate\Support\Facades\DB;

class UpdateRenewalStageAction
{
    /**
     * Atualiza o estágio da renovação ou registra a perda com motivo.
     */
    public function execute(
        PolicyRenewal $renewal,
        RenewalStageEnum $stage,
        ?RenewalLossReasonEnum $lossReason = null,
        ?string $lossNotes = null
    ): PolicyRenewal {
        return DB::transaction(function () use ($renewal, $stage, $lossReason, $lossNotes) {
            $renewal->update([
                'stage'       => $stage,
                'loss_reason' => $stage === RenewalStageEnum::Lost ? $lossReason : null,
                'loss_notes'  => $stage === RenewalStageEnum::Lost ? $lossNotes : null,
            ]);

            $renewal->policy?->update([
                'renewal_status' => $stage,
            ]);

            return $renewal->fresh();
        });
    }
}
