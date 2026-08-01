<?php

namespace App\Actions\Policy;

use App\DTOs\PolicyData;
use App\Models\Policy;

final class UpdatePolicyAction
{
    public function execute(Policy $policy, PolicyData $dto): Policy
    {
        $policy->update($dto->toUpdateArray());

        return $policy->refresh();
    }
}
