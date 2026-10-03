<?php

namespace Tests\Unit\Enums;

use App\Enums\FinancialStatusEnum;
use PHPUnit\Framework\TestCase;

class FinancialStatusEnumTest extends TestCase
{
    public function test_all_cases(): void
    {
        $this->assertCount(4, FinancialStatusEnum::cases());
    }

    public function test_labels_colors_and_icons(): void
    {
        $this->assertEquals('Pendente', FinancialStatusEnum::Pending->getLabel());
        $this->assertEquals('warning', FinancialStatusEnum::Pending->getColor());
        $this->assertEquals('heroicon-o-clock', FinancialStatusEnum::Pending->getIcon());
        $this->assertStringContainsString('bg-amber-100', FinancialStatusEnum::Pending->badgeClasses());

        $this->assertEquals('Pago / Liquidado', FinancialStatusEnum::Paid->getLabel());
        $this->assertEquals('success', FinancialStatusEnum::Paid->getColor());
        $this->assertEquals('heroicon-o-check-circle', FinancialStatusEnum::Paid->getIcon());

        $this->assertEquals('Em Atraso', FinancialStatusEnum::Overdue->getLabel());
        $this->assertEquals('danger', FinancialStatusEnum::Overdue->getColor());

        $this->assertEquals('Cancelado', FinancialStatusEnum::Canceled->getLabel());
        $this->assertEquals('gray', FinancialStatusEnum::Canceled->getColor());
    }

    public function test_options(): void
    {
        $options = FinancialStatusEnum::options();
        $this->assertIsArray($options);
        $this->assertArrayHasKey('pending', $options);
        $this->assertEquals('Pendente', $options['pending']);
        $this->assertArrayHasKey('paid', $options);
        $this->assertEquals('Pago / Liquidado', $options['paid']);
    }
}
