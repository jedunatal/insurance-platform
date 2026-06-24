<?php

namespace App\Actions\Lead;

use App\Models\Lead;

final class DeleteLeadAction
{
    public function execute(Lead $lead): void
    {
        $lead->delete();
    }
}