<?php

namespace App\Actions\Insured;

use App\DTOs\InsuredDTO;
use App\Models\Insured;

final class UpdateInsuredAction
{
    public function execute(Insured $insured, InsuredDTO $dto): Insured
    {
        $insured->update($dto->toArray());

        return $insured->refresh();
    }
}
