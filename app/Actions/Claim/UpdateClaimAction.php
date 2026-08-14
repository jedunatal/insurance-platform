<?php

namespace App\Actions\Claim;

use App\DTOs\ClaimData;
use App\Models\Claim;
use Illuminate\Support\Facades\DB;

class UpdateClaimAction
{
    public function execute(Claim $claim, ClaimData $dto): Claim
    {
        return DB::transaction(function () use ($claim, $dto) {
            $claim->update($dto->toArray());
            return $claim->fresh();
        });
    }
}