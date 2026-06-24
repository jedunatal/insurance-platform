<?php

namespace App\Actions\Lead;

use App\DTOs\LeadDTO;
use App\Models\Lead;

final class UpdateLeadAction
{
    public function execute(Lead $lead, LeadDTO $dto): Lead
    {
        $lead->update($dto->toArray());

        return $lead->refresh();
    }
}