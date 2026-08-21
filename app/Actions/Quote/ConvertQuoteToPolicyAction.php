<?php

namespace App\Actions\Quote;

use App\Actions\Financial\GeneratePolicyInstallmentsAction;
use App\Actions\Lead\ConvertLeadToInsuredAction;
use App\Enums\PolicyStatusEnum;
use App\Enums\QuoteStatusEnum;
use App\Models\Insured;
use App\Models\Policy;
use App\Models\Quote;
use App\Models\QuoteOption;
use Illuminate\Support\Facades\DB;

class ConvertQuoteToPolicyAction
{
    /**
     * Converte uma opção de cotação aprovada diretamente em uma Apólice emitida.
     */
    public function execute(Quote $quote, QuoteOption $acceptedOption): Policy
    {
        return DB::transaction(function () use ($quote, $acceptedOption) {
            // Se a cotação estava vinculada a um Lead, converte o Lead em Segurado
            $insuredId = $quote->insured_id;
            if (! $insuredId && $quote->lead) {
                $insured = app(ConvertLeadToInsuredAction::class)->execute($quote->lead);
                $insuredId = $insured->id;
            }

            if (! $insuredId) {
                $insured = Insured::create([
                    'tenant_id' => $quote->tenant_id,
                    'name'      => $quote->title,
                ]);
                $insuredId = $insured->id;
            }

            $year = now()->year;
            $random = strtoupper(substr(uniqid(), -4));
            $policyNumber = "APOL-{$year}-{$random}";

            /** @var Policy $policy */
            $policy = Policy::create([
                'tenant_id'             => $quote->tenant_id,
                'insured_id'            => $insuredId,
                'broker_id'             => auth()->id() ?? $quote->created_by,
                'created_by'            => auth()->id() ?? $quote->created_by,
                'policy_number'         => $policyNumber,
                'proposal_number'       => "PROP-{$policyNumber}",
                'insurer'               => $acceptedOption->insurer,
                'branch'                => $quote->branch?->value ?? ($quote->branch ?? 'Automóvel'),
                'status'                => PolicyStatusEnum::Active,
                'start_date'            => now()->startOfDay(),
                'end_date'              => now()->addYear()->endOfDay(),
                'net_premium'           => $acceptedOption->net_premium,
                'iof_rate'              => 7.38,
                'iof_amount'            => $acceptedOption->iof_amount,
                'total_premium'         => $acceptedOption->total_premium,
                'deductible_amount'     => $acceptedOption->deductible_amount,
                'payment_method'        => 'invoice',
                'installments_count'    => 1,
                'notes'                 => "Gerada a partir da Cotação #{$quote->quote_number} (Opção {$acceptedOption->insurer}).",
            ]);

            // Gera grade de parcelas
            app(GeneratePolicyInstallmentsAction::class)->execute($policy);

            // Marca a opção como aceita e as demais não aceitas
            $quote->options()->update(['is_accepted' => false]);
            $acceptedOption->update(['is_accepted' => true]);

            // Atualiza status da cotação
            $quote->update([
                'status'              => QuoteStatusEnum::Converted,
                'converted_policy_id' => $policy->id,
                'insured_id'          => $insuredId,
            ]);

            return $policy->fresh();
        });
    }
}
