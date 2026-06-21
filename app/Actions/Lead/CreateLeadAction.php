<?php

namespace App\Actions\Lead;

use App\DTOs\LeadDTO;
use App\Models\Lead;

/**
 * Responsável pela criação de um Lead.
 *
 * Esta Action possui apenas uma responsabilidade:
 * persistir um novo Lead no banco de dados.
 *
 * Regras de negócio complexas devem permanecer
 * no LeadService.
 */
final class CreateLeadAction
{
    public function execute(
        LeadDTO $dto
    ): Lead {
        return Lead::create(
            $dto->toArray()
        );
    }
}