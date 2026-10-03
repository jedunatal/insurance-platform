<?php

namespace Tests\Unit\Enums;

use App\Enums\ClaimStatusEnum;
use PHPUnit\Framework\TestCase;

class ClaimStatusEnumTest extends TestCase
{
    public function test_all_cases(): void
    {
        $this->assertCount(7, ClaimStatusEnum::cases());
    }

    public function test_labels_colors_icons_and_dots(): void
    {
        $this->assertEquals('Avisado', ClaimStatusEnum::Reported->getLabel());
        $this->assertEquals('info', ClaimStatusEnum::Reported->getColor());
        $this->assertEquals('heroicon-m-megaphone', ClaimStatusEnum::Reported->getIcon());
        $this->assertEquals('bg-blue-500', ClaimStatusEnum::Reported->dotColor());
        $this->assertStringContainsString('bg-blue-100', ClaimStatusEnum::Reported->badgeClasses());

        $this->assertEquals('Em Análise', ClaimStatusEnum::UnderAnalysis->getLabel());
        $this->assertEquals('warning', ClaimStatusEnum::UnderAnalysis->getColor());

        $this->assertEquals('Vistoria / Orçamento', ClaimStatusEnum::Inspection->getLabel());

        $this->assertEquals('Aprovado', ClaimStatusEnum::Approved->getLabel());
        $this->assertEquals('success', ClaimStatusEnum::Approved->getColor());

        $this->assertEquals('Indenizado / Concluído', ClaimStatusEnum::Indemnified->getLabel());
        $this->assertEquals('success', ClaimStatusEnum::Indemnified->getColor());

        $this->assertEquals('Recusado', ClaimStatusEnum::Rejected->getLabel());
        $this->assertEquals('danger', ClaimStatusEnum::Rejected->getColor());

        $this->assertEquals('Cancelado', ClaimStatusEnum::Cancelled->getLabel());
    }

    public function test_options(): void
    {
        $options = ClaimStatusEnum::options();
        $this->assertIsArray($options);
        $this->assertArrayHasKey('reported', $options);
        $this->assertEquals('Avisado', $options['reported']);
        $this->assertArrayHasKey('indemnified', $options);
        $this->assertEquals('Indenizado / Concluído', $options['indemnified']);
    }
}
