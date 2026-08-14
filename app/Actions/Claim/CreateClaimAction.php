<?php

namespace App\Actions\Claim;

use App\DTOs\ClaimData;
use App\Models\Claim;
use Illuminate\Support\Facades\DB;

class CreateClaimAction
{
    public function execute(ClaimData $dto): Claim
    {
        return DB::transaction(function () use ($dto) {
            return Claim::create($dto->toArray());
        });
    }
}