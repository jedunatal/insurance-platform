<?php

namespace Tests\Unit\Enums;

use App\Enums\LeadSourceEnum;
use PHPUnit\Framework\TestCase;

class LeadSourceEnumTest extends TestCase
{
    public function test_all_cases(): void
    {
        $this->assertCount(7, LeadSourceEnum::cases());
    }

    public function test_labels_emojis_and_colors(): void
    {
        $this->assertEquals('Site', LeadSourceEnum::Site->label());
        $this->assertEquals('🌐', LeadSourceEnum::Site->emoji());
        $this->assertEquals('text-slate-600', LeadSourceEnum::Site->colorClass());

        $this->assertEquals('WhatsApp', LeadSourceEnum::Whatsapp->label());
        $this->assertEquals('💬', LeadSourceEnum::Whatsapp->emoji());
        $this->assertEquals('text-green-600', LeadSourceEnum::Whatsapp->colorClass());

        $this->assertEquals('Indicação', LeadSourceEnum::Referral->label());
        $this->assertEquals('🤝', LeadSourceEnum::Referral->emoji());
    }

    public function test_options_format(): void
    {
        $options = LeadSourceEnum::options();
        $this->assertIsArray($options);
        $this->assertArrayHasKey('site', $options);
        $this->assertEquals('Site', $options['site']);
        $this->assertArrayHasKey('whatsapp', $options);
        $this->assertEquals('WhatsApp', $options['whatsapp']);
    }
}
