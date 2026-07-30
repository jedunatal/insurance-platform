<?php

namespace App\Actions\Insured;

use App\DTOs\InsuredDTO;
use App\Models\Insured;

/**
 * Responsável pela criação de um Segurado.
 *
 * Regras de negócio complexas devem permanecer
 * no InsuredService.
 */
final class CreateInsuredAction
{
    public function execute(InsuredDTO $dto): Insured
    {
        return Insured::create($dto->toArray());
    }
}
