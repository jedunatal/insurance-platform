<?php

namespace Tests\Unit\Models;

use App\Enums\FinancialStatusEnum;
use App\Models\PolicyInstallment;
use Carbon\Carbon;
use Tests\TestCase;

class PolicyInstallmentFormattingTest extends TestCase
{
    public function test_formatting_helpers(): void
    {
        $installment = new PolicyInstallment();
        $installment->installment_number = 3;
        $installment->total_installments = 12;
        $installment->gross_amount = 350.50;
        $installment->commission_expected = 52.58;
        $installment->commission_received = 52.58;

        $this->assertEquals('3/12', $installment->formattedInstallment());
        $this->assertEquals('R$ 350,50', $installment->formattedGrossAmount());
        $this->assertEquals('R$ 52,58', $installment->formattedCommissionExpected());
        $this->assertEquals('R$ 52,58', $installment->formattedCommissionReceived());
    }

    public function test_commission_received_placeholder_when_null(): void
    {
        $installment = new PolicyInstallment();
        $installment->commission_received = null;

        $this->assertEquals('-', $installment->formattedCommissionReceived());
    }

    public function test_is_paid_and_is_overdue(): void
    {
        $paid = new PolicyInstallment();
        $paid->status = FinancialStatusEnum::Paid;
        $this->assertTrue($paid->isPaid());
        $this->assertFalse($paid->isOverdue());

        $overdue = new PolicyInstallment();
        $overdue->status = FinancialStatusEnum::Overdue;
        $this->assertFalse($overdue->isPaid());
        $this->assertTrue($overdue->isOverdue());

        $pendingPast = new PolicyInstallment();
        $pendingPast->status = FinancialStatusEnum::Pending;
        $pendingPast->due_date = Carbon::yesterday();
        $this->assertTrue($pendingPast->isOverdue());
    }
}
