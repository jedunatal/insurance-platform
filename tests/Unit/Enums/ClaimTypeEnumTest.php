<?php

namespace Tests\Unit\Enums;

use App\Enums\ClaimTypeEnum;
use PHPUnit\Framework\TestCase;

class ClaimTypeEnumTest extends TestCase
{
    public function test_all_cases(): void
    {
        $this->assertCount(9, ClaimTypeEnum::cases());
    }

    public function test_labels_colors_and_icons(): void
    {
        $this->assertEquals('Colisão', ClaimTypeEnum::Collision->getLabel());
        $this->assertEquals('warning', ClaimTypeEnum::Collision->getColor());
        $this->assertEquals('heroicon-o-truck', ClaimTypeEnum::Collision->getIcon());

        $this->assertEquals('Roubo / Furto', ClaimTypeEnum::Theft->getLabel());
        $this->assertEquals('danger', ClaimTypeEnum::Theft->getColor());

        $this->assertEquals('Danos a Terceiros', ClaimTypeEnum::ThirdParty->getLabel());
        $this->assertEquals('Incêndio / Queda de Raio', ClaimTypeEnum::Fire->getLabel());
        $this->assertEquals('Vidros / Retrovisores / Faróis', ClaimTypeEnum::Glass->getLabel());
    }

    public function test_options(): void
    {
        $options = ClaimTypeEnum::options();
        $this->assertIsArray($options);
        $this->assertArrayHasKey('collision', $options);
        $this->assertEquals('Colisão', $options['collision']);
        $this->assertArrayHasKey('theft', $options);
        $this->assertEquals('Roubo / Furto', $options['theft']);
    }
}
