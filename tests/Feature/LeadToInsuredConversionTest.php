<?php

namespace Tests\Feature;

use App\Actions\Insured\ConvertLeadToInsuredAction;
use App\Enums\LeadStatusEnum;
use App\Enums\PersonTypeEnum;
use App\Models\Insured;
use App\Models\Lead;
use App\Models\Tenant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LeadToInsuredConversionTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_convert_lead_to_insured_with_transaction(): void
    {
        $tenant = Tenant::create([
            'name' => 'Corretora Prime',
            'slug' => 'corretora-prime',
            'email' => 'contato@prime.com',
            'document' => '12345678000199',
        ]);

        $lead = Lead::create([
            'tenant_id' => $tenant->id,
            'name' => 'Carlos Alberto Santos',
            'email' => 'carlos@santos.com',
            'phone' => '11988887777',
            'document' => '12345678901',
            'status' => LeadStatusEnum::New->value,
            'notes' => 'Interesse em seguro auto.',
        ]);

        $action = app(ConvertLeadToInsuredAction::class);
        $insured = $action->execute($lead, [
            'city' => 'São Paulo',
            'state' => 'SP',
            'birth_date' => '1985-06-15',
        ]);

        $this->assertInstanceOf(Insured::class, $insured);
        $this->assertEquals('Carlos Alberto Santos', $insured->name);
        $this->assertEquals('carlos@santos.com', $insured->email);
        $this->assertEquals('11988887777', $insured->phone);
        $this->assertEquals('12345678901', $insured->document);
        $this->assertEquals(PersonTypeEnum::Individual, $insured->person_type);
        $this->assertEquals('1985-06-15', $insured->birth_date->format('Y-m-d'));
        $this->assertEquals('São Paulo', $insured->city);
        $this->assertEquals('SP', $insured->state);
        $this->assertEquals($lead->id, $insured->lead_id);

        // Verifica se o status do Lead foi atualizado para Convertido
        $lead->refresh();
        $this->assertEquals(LeadStatusEnum::Converted, $lead->status);

        // Testa o relacionamento no Eloquent
        $this->assertTrue($lead->insured->is($insured));
        $this->assertTrue($insured->lead->is($lead));
    }
}
