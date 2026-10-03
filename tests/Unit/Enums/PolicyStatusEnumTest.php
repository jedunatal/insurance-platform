<?php

namespace Tests\Unit\Enums;

use App\Enums\PolicyStatusEnum;
use PHPUnit\Framework\TestCase;

class PolicyStatusEnumTest extends TestCase
{
    public function test_all_cases(): void
    {
        $this->assertCount(6, PolicyStatusEnum::cases());
    }

    public function test_labels_and_colors(): void
    {
        $this->assertEquals('Vigente', PolicyStatusEnum::Active->label());
        $this->assertEquals('Vigente', PolicyStatusEnum::Active->shortLabel());
        $this->assertEquals('success', PolicyStatusEnum::Active->color());
        $this->assertEquals('heroicon-o-shield-check', PolicyStatusEnum::Active->icon());
        $this->assertEquals('bg-green-500', PolicyStatusEnum::Active->dotColor());
        $this->assertStringContainsString('bg-green-100', PolicyStatusEnum::Active->badgeClasses());

        $this->assertEquals('Rascunho', PolicyStatusEnum::Draft->label());
        $this->assertEquals('gray', PolicyStatusEnum::Draft->color());

        $this->assertEquals('Renovação Pendente', PolicyStatusEnum::PendingRenewal->label());
        $this->assertEquals('Renovar', PolicyStatusEnum::PendingRenewal->shortLabel());
        $this->assertEquals('warning', PolicyStatusEnum::PendingRenewal->color());

        $this->assertEquals('Cancelada', PolicyStatusEnum::Cancelled->label());
        $this->assertEquals('danger', PolicyStatusEnum::Cancelled->color());
    }

    public function test_state_helpers(): void
    {
        // isActive
        $this->assertTrue(PolicyStatusEnum::Active->isActive());
        $this->assertTrue(PolicyStatusEnum::PendingRenewal->isActive());
        $this->assertFalse(PolicyStatusEnum::Draft->isActive());
        $this->assertFalse(PolicyStatusEnum::Cancelled->isActive());

        // isEditable
        $this->assertTrue(PolicyStatusEnum::Draft->isEditable());
        $this->assertTrue(PolicyStatusEnum::PendingRenewal->isEditable());
        $this->assertFalse(PolicyStatusEnum::Active->isEditable());

        // isDeletable
        $this->assertTrue(PolicyStatusEnum::Draft->isDeletable());
        $this->assertTrue(PolicyStatusEnum::Cancelled->isDeletable());
        $this->assertTrue(PolicyStatusEnum::Expired->isDeletable());
        $this->assertFalse(PolicyStatusEnum::Active->isDeletable());

        // scopeName
        $this->assertEquals('active', PolicyStatusEnum::Active->scopeName());
        $this->assertEquals('draft', PolicyStatusEnum::Draft->scopeName());
    }

    public function test_options(): void
    {
        $options = PolicyStatusEnum::options();
        $this->assertIsArray($options);
        $this->assertArrayHasKey('active', $options);
        $this->assertEquals('Vigente', $options['active']);
        $this->assertArrayHasKey('draft', $options);
        $this->assertEquals('Rascunho', $options['draft']);
    }
}
