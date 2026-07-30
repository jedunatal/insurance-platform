<?php

namespace App\Actions\Insured;

use App\Models\Insured;

final class DeleteInsuredAction
{
    public function execute(Insured $insured): void
    {
        $insured->delete();
    }
}
