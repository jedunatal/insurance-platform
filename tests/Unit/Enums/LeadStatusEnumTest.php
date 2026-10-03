<?php

namespace Tests\Unit\Enums;

use App\Enums\LeadStatusEnum;
use PHPUnit\Framework\TestCase;

class LeadStatusEnumTest extends TestCase
{
    public function test_all_cases(): void
    {
        $this->assertCount(6, LeadStatusEnum::cases());
    }

    public function test_is_active_logic(): void
    {
        $this->assertTrue(LeadStatusEnum::New->isActive());
        $this->assertTrue(LeadStatusEnum::Contact->isActive());
        $this->assertTrue(LeadStatusEnum::InNegotiation->isActive());
        $this->assertTrue(LeadStatusEnum::Proposal->isActive());

        // Converted and Lost should not be active
        $this->assertFalse(LeadStatusEnum::Converted->isActive());
        $this->assertFalse(LeadStatusEnum::Lost->isActive());
    }

    public function test_labels_colors_and_dots(): void
    {
        $this->assertEquals('Novo', LeadStatusEnum::New->getLabel());
        $this->assertEquals('info', LeadStatusEnum::New->getColor());
        $this->assertEquals('bg-blue-500', LeadStatusEnum::New->dotColor());
        $this->assertStringContainsString('bg-blue-100', LeadStatusEnum::New->badgeClasses());

        $this->assertEquals('Convertido', LeadStatusEnum::Converted->getLabel());
        $this->assertEquals('success', LeadStatusEnum::Converted->getColor());
        $this->assertEquals('bg-green-500', LeadStatusEnum::Converted->dotColor());

        $this->assertEquals('Perdido', LeadStatusEnum::Lost->getLabel());
        $this->assertEquals('danger', LeadStatusEnum::Lost->getColor());
    }

    public function test_options_and_from_value(): void
    {
        $options = LeadStatusEnum::options();
        $this->assertIsArray($options);
        $this->assertArrayHasKey('Novo', $options);
        $this->assertArrayHasKey('Convertido', $options);

        $this->assertSame(LeadStatusEnum::New, LeadStatusEnum::fromValue('Novo'));
        $this->assertSame(LeadStatusEnum::Converted, LeadStatusEnum::fromValue('Convertido'));
    }
}
