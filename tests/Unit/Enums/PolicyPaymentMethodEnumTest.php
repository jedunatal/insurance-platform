<?php

namespace Tests\Unit\Enums;

use App\Enums\PolicyPaymentMethodEnum;
use PHPUnit\Framework\TestCase;

class PolicyPaymentMethodEnumTest extends TestCase
{
    public function test_all_cases(): void
    {
        $this->assertCount(4, PolicyPaymentMethodEnum::cases());
    }

    public function test_labels_icons_and_recurring(): void
    {
        $this->assertEquals('Cartão de Crédito', PolicyPaymentMethodEnum::CreditCard->label());
        $this->assertEquals('Cartão', PolicyPaymentMethodEnum::CreditCard->shortLabel());
        $this->assertEquals('heroicon-o-credit-card', PolicyPaymentMethodEnum::CreditCard->icon());
        $this->assertTrue(PolicyPaymentMethodEnum::CreditCard->isRecurring());

        $this->assertEquals('Débito em Conta', PolicyPaymentMethodEnum::Debit->label());
        $this->assertEquals('Débito', PolicyPaymentMethodEnum::Debit->shortLabel());
        $this->assertTrue(PolicyPaymentMethodEnum::Debit->isRecurring());

        $this->assertEquals('Boleto Bancário', PolicyPaymentMethodEnum::Invoice->label());
        $this->assertEquals('Boleto', PolicyPaymentMethodEnum::Invoice->shortLabel());
        $this->assertFalse(PolicyPaymentMethodEnum::Invoice->isRecurring());

        $this->assertEquals('Pix', PolicyPaymentMethodEnum::Pix->label());
        $this->assertEquals('Pix', PolicyPaymentMethodEnum::Pix->shortLabel());
        $this->assertFalse(PolicyPaymentMethodEnum::Pix->isRecurring());
    }

    public function test_options(): void
    {
        $options = PolicyPaymentMethodEnum::options();
        $this->assertIsArray($options);
        $this->assertArrayHasKey('credit_card', $options);
        $this->assertEquals('Cartão de Crédito', $options['credit_card']);
        $this->assertArrayHasKey('invoice', $options);
        $this->assertEquals('Boleto Bancário', $options['invoice']);
    }
}
