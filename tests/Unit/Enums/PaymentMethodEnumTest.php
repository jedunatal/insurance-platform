<?php

namespace Tests\Unit\Enums;

use App\Enums\PaymentMethodEnum;
use PHPUnit\Framework\TestCase;

class PaymentMethodEnumTest extends TestCase
{
    public function test_all_cases(): void
    {
        $this->assertCount(6, PaymentMethodEnum::cases());
    }

    public function test_labels_colors_and_icons(): void
    {
        $this->assertEquals('Boleto Bancário', PaymentMethodEnum::Invoice->getLabel());
        $this->assertEquals('info', PaymentMethodEnum::Invoice->getColor());
        $this->assertEquals('heroicon-o-document-text', PaymentMethodEnum::Invoice->getIcon());

        $this->assertEquals('Cartão de Crédito', PaymentMethodEnum::CreditCard->getLabel());
        $this->assertEquals('primary', PaymentMethodEnum::CreditCard->getColor());

        $this->assertEquals('Débito em Conta', PaymentMethodEnum::Debit->getLabel());
        $this->assertEquals('PIX', PaymentMethodEnum::Pix->getLabel());
        $this->assertEquals('Desconto em Folha', PaymentMethodEnum::Payroll->getLabel());
        $this->assertEquals('Outro', PaymentMethodEnum::Other->getLabel());
    }

    public function test_options(): void
    {
        $options = PaymentMethodEnum::options();
        $this->assertIsArray($options);
        $this->assertArrayHasKey('invoice', $options);
        $this->assertEquals('Boleto Bancário', $options['invoice']);
        $this->assertArrayHasKey('pix', $options);
        $this->assertEquals('PIX', $options['pix']);
    }
}
