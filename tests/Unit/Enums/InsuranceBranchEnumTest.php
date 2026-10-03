<?php

namespace Tests\Unit\Enums;

use App\Enums\InsuranceBranchEnum;
use PHPUnit\Framework\TestCase;

class InsuranceBranchEnumTest extends TestCase
{
    public function test_all_cases_exist(): void
    {
        $cases = InsuranceBranchEnum::cases();
        $this->assertCount(9, $cases);
    }

    public function test_default_iof_rates(): void
    {
        $this->assertEquals(7.38, InsuranceBranchEnum::Auto->defaultIofRate());
        $this->assertEquals(7.38, InsuranceBranchEnum::Home->defaultIofRate());
        $this->assertEquals(7.38, InsuranceBranchEnum::Business->defaultIofRate());
        $this->assertEquals(7.38, InsuranceBranchEnum::Electronics->defaultIofRate());
        $this->assertEquals(7.38, InsuranceBranchEnum::Liability->defaultIofRate());
        $this->assertEquals(7.38, InsuranceBranchEnum::Other->defaultIofRate());
        $this->assertEquals(0.38, InsuranceBranchEnum::Life->defaultIofRate());
        $this->assertEquals(0.38, InsuranceBranchEnum::Health->defaultIofRate());
        $this->assertEquals(0.00, InsuranceBranchEnum::Rural->defaultIofRate());
    }

    public function test_labels_and_colors(): void
    {
        $this->assertEquals('Automóvel', InsuranceBranchEnum::Auto->getLabel());
        $this->assertEquals('Automóvel', InsuranceBranchEnum::Auto->label());
        $this->assertEquals('info', InsuranceBranchEnum::Auto->getColor());
        $this->assertEquals('heroicon-o-truck', InsuranceBranchEnum::Auto->getIcon());

        $this->assertEquals('Vida', InsuranceBranchEnum::Life->getLabel());
        $this->assertEquals('success', InsuranceBranchEnum::Life->getColor());
        $this->assertEquals('heroicon-o-heart', InsuranceBranchEnum::Life->getIcon());

        $this->assertEquals('Agrícola / Rural', InsuranceBranchEnum::Rural->getLabel());
    }

    public function test_options_returns_valid_array(): void
    {
        $options = InsuranceBranchEnum::options();

        $this->assertIsArray($options);
        $this->assertArrayHasKey('Automóvel', $options);
        $this->assertArrayHasKey('Vida', $options);
        $this->assertArrayHasKey('Residencial', $options);
        $this->assertEquals('Automóvel', $options['Automóvel']);
    }
}
