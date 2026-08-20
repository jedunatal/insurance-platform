<?php

namespace App\Actions\Policy;

use App\Actions\Financial\GeneratePolicyInstallmentsAction;
use App\DTOs\PolicyData;
use App\Models\Policy;
use Illuminate\Support\Facades\DB;

/**
 * Responsável pela criação de uma Apólice e geração automática de suas parcelas financeiras.
 */
final class CreatePolicyAction
{
    public function execute(PolicyData $dto): Policy
    {
        return DB::transaction(function () use ($dto) {
            $policy = Policy::create($dto->toArray());

            // Gera e persiste as parcelas financeiras e comissões da apólice
            app(GeneratePolicyInstallmentsAction::class)->execute($policy);

            return $policy->fresh();
        });
    }
}
