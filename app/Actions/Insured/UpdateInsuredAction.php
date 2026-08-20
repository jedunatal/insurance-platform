<?php

namespace App\Actions\Insured;

use App\DTOs\InsuredDTO;
use App\Models\Insured;
use Illuminate\Support\Facades\DB;

final class UpdateInsuredAction
{
    public function execute(Insured $insured, InsuredDTO $dto): Insured
    {
        return DB::transaction(function () use ($insured, $dto) {
            $insured->update($dto->toArray());
            return $insured->refresh();
        });
    }
}
