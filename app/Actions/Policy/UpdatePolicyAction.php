<?php

namespace App\Actions\Policy;

use App\DTOs\PolicyData;
use App\Models\Policy;
use Illuminate\Support\Facades\DB;

final class UpdatePolicyAction
{
    public function execute(Policy $policy, PolicyData $dto): Policy
    {
        return DB::transaction(function () use ($policy, $dto) {
            $policy->update($dto->toUpdateArray());
            return $policy->refresh();
        });
    }
}
