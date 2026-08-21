<?php

namespace App\Actions\Quote;

use App\Enums\QuoteStatusEnum;
use App\Models\Quote;
use App\Models\QuoteOption;
use Illuminate\Support\Facades\DB;

class CreateQuoteAction
{
    /**
     * Cria uma cotação com suas opções comparativas multi-seguradoras.
     *
     * @param array<string, mixed> $data
     * @param array<int, array<string, mixed>> $options
     */
    public function execute(array $data, array $options = []): Quote
    {
        return DB::transaction(function () use ($data, $options) {
            $year = now()->year;
            $count = Quote::whereYear('created_at', $year)->count() + 1;
            $quoteNumber = 'COT-' . $year . '-' . str_pad((string) $count, 4, '0', STR_PAD_LEFT);

            /** @var Quote $quote */
            $quote = Quote::create([
                'tenant_id'    => $data['tenant_id'] ?? (auth()->user()?->tenant_id ?? 1),
                'lead_id'      => $data['lead_id'] ?? null,
                'insured_id'   => $data['insured_id'] ?? null,
                'created_by'   => auth()->id() ?? ($data['created_by'] ?? null),
                'quote_number' => $data['quote_number'] ?? $quoteNumber,
                'title'        => $data['title'] ?? 'Estudo de Cotação de Seguro',
                'branch'       => $data['branch'] ?? 'Automóvel',
                'status'       => $data['status'] ?? QuoteStatusEnum::Draft,
                'valid_until'  => $data['valid_until'] ?? now()->addDays(15),
                'risk_data'    => $data['risk_data'] ?? null,
                'notes'        => $data['notes'] ?? null,
            ]);

            foreach ($options as $opt) {
                QuoteOption::create([
                    'quote_id'               => $quote->id,
                    'insurer'                => $opt['insurer'] ?? 'Seguradora',
                    'product_name'           => $opt['product_name'] ?? null,
                    'net_premium'            => (float) ($opt['net_premium'] ?? 0),
                    'iof_amount'             => (float) ($opt['iof_amount'] ?? 0),
                    'total_premium'          => (float) ($opt['total_premium'] ?? 0),
                    'deductible_type'        => $opt['deductible_type'] ?? 'normal',
                    'deductible_amount'      => (float) ($opt['deductible_amount'] ?? 0),
                    'car_rental'             => $opt['car_rental'] ?? null,
                    'glass_coverage'         => $opt['glass_coverage'] ?? null,
                    'third_party_materials'  => (float) ($opt['third_party_materials'] ?? 0),
                    'third_party_corporal'   => (float) ($opt['third_party_corporal'] ?? 0),
                    'app_coverage'           => (float) ($opt['app_coverage'] ?? 0),
                    'payment_conditions'     => $opt['payment_conditions'] ?? null,
                    'is_recommended'         => (bool) ($opt['is_recommended'] ?? false),
                    'is_accepted'            => (bool) ($opt['is_accepted'] ?? false),
                    'highlights'             => $opt['highlights'] ?? null,
                    'notes'                  => $opt['notes'] ?? null,
                ]);
            }

            return $quote->fresh(['options', 'lead', 'insured']);
        });
    }
}
