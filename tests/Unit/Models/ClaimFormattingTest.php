<?php

namespace Tests\Unit\Models;

use App\Models\Claim;
use Tests\TestCase;

class ClaimFormattingTest extends TestCase
{
    public function test_formatting_amounts(): void
    {
        $claim = new Claim();
        $claim->estimated_amount = 4500.50;
        $claim->indemnified_amount = 4000.00;
        $claim->deductible_amount = 1200.75;

        $this->assertEquals('R$ 4.500,50', $claim->formattedEstimatedAmount());
        $this->assertEquals('R$ 4.000,00', $claim->formattedIndemnifiedAmount());
        $this->assertEquals('R$ 1.200,75', $claim->formattedDeductibleAmount());
    }

    public function test_formatting_with_null_or_zero(): void
    {
        $claim = new Claim();
        $claim->estimated_amount = 0;
        $claim->indemnified_amount = null;

        $this->assertEquals('R$ 0,00', $claim->formattedEstimatedAmount());
        $this->assertEquals('R$ 0,00', $claim->formattedIndemnifiedAmount());
    }
}
