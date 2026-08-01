<?php

namespace App\Actions\Policy;

use App\Models\Policy;

final class DeletePolicyAction
{
    public function execute(Policy $policy): void
    {
        $policy->delete();
    }
}
