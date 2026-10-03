<?php

namespace Tests\Unit\Services;

use App\Actions\Policy\CreatePolicyAction;
use App\Actions\Policy\DeletePolicyAction;
use App\Actions\Policy\UpdatePolicyAction;
use App\Enums\PolicyPaymentMethodEnum;
use App\Services\Insurance\PolicyService;
use PHPUnit\Framework\TestCase;

class PolicyCalculationUnitTest extends TestCase
{
    private PolicyService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = new PolicyService(
            new CreatePolicyAction(),
            new UpdatePolicyAction(),
            new DeletePolicyAction()
        );
    }

    public function test_calculate_iof(): void
    {
        // Net premium 1000.00 * 0.0738 = 73.80
        $iof = $this->service->calculateIof(1000.00);
        $this->assertEquals(73.80, $iof);

        // Net premium 2543.50 * 0.0738 = 187.7103 -> 187.71
        $iof2 = $this->service->calculateIof(2543.50);
        $this->assertEquals(187.71, $iof2);
    }

    public function test_calculate_total_premium(): void
    {
        // 1000.00 + 73.80 = 1073.80
        $total = $this->service->calculateTotalPremium(1000.00);
        $this->assertEquals(1073.80, $total);

        // Custom IOF passed
        $totalCustom = $this->service->calculateTotalPremium(1000.00, 50.00);
        $this->assertEquals(1050.00, $totalCustom);
    }

    public function test_recalculate_premiums_returns_expected_structure(): void
    {
        $result = $this->service->recalculatePremiums(2000.00);

        $this->assertIsArray($result);
        $this->assertEquals(2000.00, $result['net_premium']);
        $this->assertEquals(147.60, $result['iof_amount']);
        $this->assertEquals(2147.60, $result['total_premium']);
    }

    public function test_generate_installment_schedule_single_installment(): void
    {
        $schedule = $this->service->generateInstallmentSchedule(
            totalPremium: 1500.00,
            installmentsCount: 1,
            method: PolicyPaymentMethodEnum::Invoice,
            firstDueDate: '2026-10-10'
        );

        $this->assertCount(1, $schedule);
        $this->assertEquals(1, $schedule[0]['installment']);
        $this->assertEquals(1500.00, $schedule[0]['amount']);
        $this->assertEquals('2026-10-10', $schedule[0]['due_date']);
        $this->assertEquals('pending', $schedule[0]['status']);
        $this->assertEquals('invoice', $schedule[0]['method']);
    }

    public function test_generate_installment_schedule_handles_odd_division_cents_rounding(): void
    {
        // 1000.00 / 3 = 333.33 each, remainder 0.01 adjusted on final installment
        $schedule = $this->service->generateInstallmentSchedule(
            totalPremium: 1000.00,
            installmentsCount: 3,
            method: PolicyPaymentMethodEnum::CreditCard,
            firstDueDate: '2026-10-01'
        );

        $this->assertCount(3, $schedule);
        $this->assertEquals('2026-10-01', $schedule[0]['due_date']);
        $this->assertEquals('2026-11-01', $schedule[1]['due_date']);
        $this->assertEquals('2026-12-01', $schedule[2]['due_date']);

        $sum = array_sum(array_column($schedule, 'amount'));
        $this->assertEquals(1000.00, round($sum, 2));
    }
}
