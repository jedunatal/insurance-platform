<?php

namespace App\Actions\Policy;

use App\DTOs\PolicyData;
use App\Models\Policy;
use Illuminate\Support\Facades\DB;

/**
 * Responsável pela criação de uma Apólice.
 */
final class CreatePolicyAction
{
    public function execute(PolicyData $dto): Policy
    {
        return DB::transaction(function () use ($dto) {
            return Policy::create($dto->toArray());
        });
    }
}
