<?php

namespace App\Actions\Policy;

use App\DTOs\PolicyData;
use App\Models\Policy;

/**
 * Responsável pela criação de uma Apólice.
 *
 * Regras de negócio complexas (recálculo de prêmio, geração de
 * parcelas, auditoria) devem permanecer no PolicyService.
 */
final class CreatePolicyAction
{
    public function execute(PolicyData $dto): Policy
    {
        return Policy::create($dto->toArray());
    }
}
