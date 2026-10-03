<?php

namespace Tests\Unit\Enums;

use App\Enums\PersonTypeEnum;
use PHPUnit\Framework\TestCase;

class PersonTypeEnumTest extends TestCase
{
    public function test_individual_person_properties(): void
    {
        $type = PersonTypeEnum::Individual;

        $this->assertEquals('PF', $type->value);
        $this->assertEquals('Pessoa Física', $type->label());
        $this->assertEquals('Pessoa Física', $type->getLabel());
        $this->assertEquals('PF', $type->shortLabel());
        $this->assertEquals('CPF', $type->documentLabel());
        $this->assertEquals('999.999.999-99', $type->documentMask());
        $this->assertEquals('000.000.000-00', $type->documentPlaceholder());
        $this->assertEquals(11, $type->documentLength());
        $this->assertStringContainsString('bg-blue-100', $type->badgeClasses());
    }

    public function test_legal_person_properties(): void
    {
        $type = PersonTypeEnum::Legal;

        $this->assertEquals('PJ', $type->value);
        $this->assertEquals('Pessoa Jurídica', $type->label());
        $this->assertEquals('Pessoa Jurídica', $type->getLabel());
        $this->assertEquals('PJ', $type->shortLabel());
        $this->assertEquals('CNPJ', $type->documentLabel());
        $this->assertEquals('99.999.999/9999-99', $type->documentMask());
        $this->assertEquals('00.000.000/0000-00', $type->documentPlaceholder());
        $this->assertEquals(14, $type->documentLength());
        $this->assertStringContainsString('bg-amber-100', $type->badgeClasses());
    }

    public function test_options(): void
    {
        $options = PersonTypeEnum::options();
        $this->assertIsArray($options);
        $this->assertArrayHasKey('PF', $options);
        $this->assertEquals('Pessoa Física', $options['PF']);
        $this->assertArrayHasKey('PJ', $options);
        $this->assertEquals('Pessoa Jurídica', $options['PJ']);
    }
}
