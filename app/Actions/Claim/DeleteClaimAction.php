<?php

namespace App\Actions\Claim;

use App\Models\Claim;
use Illuminate\Support\Facades\DB;

class DeleteClaimAction
{
    public function execute(Claim $claim): bool
    {
        return DB::transaction(function () use ($claim) {
            return $claim->delete();
        });
    }
}