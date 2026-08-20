<?php

namespace App\Actions\Insured;

use App\DTOs\InsuredDTO;
use App\Enums\LeadStatusEnum;
use App\Models\Insured;
use App\Models\Lead;
use Illuminate\Support\Facades\DB;

/**
 * Responsável pela criação de um Segurado.
 */
final class CreateInsuredAction
{
    public function execute(InsuredDTO $dto): Insured
    {
        return DB::transaction(function () use ($dto) {
            $insured = Insured::create($dto->toArray());

            if ($dto->leadId) {
                Lead::where('id', $dto->leadId)->update([
                    'status' => LeadStatusEnum::Converted->value,
                ]);
            }

            return $insured;
        });
    }
}
