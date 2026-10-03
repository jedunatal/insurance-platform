<?php

namespace Tests\Feature;

use App\Actions\Insured\CreateInsuredAction;
use App\Actions\Insured\DeleteInsuredAction;
use App\Actions\Insured\UpdateInsuredAction;
use App\DTOs\InsuredDTO;
use App\Enums\LeadSourceEnum;
use App\Enums\LeadStatusEnum;
use App\Enums\PersonTypeEnum;
use App\Models\Insured;
use App\Models\Lead;
use App\Models\Tenant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InsuredActionsTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_insured_and_automatically_marks_lead_as_converted(): void
    {
        $tenant = Tenant::create([
            'name' => 'Corretora Sigma',
            'slug' => 'corretora-sigma',
            'email' => 'sigma@corretora.com',
            'document' => '44555666000177',
        ]);

        $lead = Lead::create([
            'tenant_id' => $tenant->id,
            'name' => 'Bruno Alencar',
            'email' => 'bruno@alencar.com',
            'phone' => '11988889999',
            'source' => LeadSourceEnum::Site->value,
            'status' => LeadStatusEnum::Proposal->value,
        ]);

        $dto = InsuredDTO::fromArray([
            'tenant_id' => $tenant->id,
            'lead_id' => $lead->id,
            'name' => 'Bruno Alencar',
            'email' => 'bruno@alencar.com',
            'phone' => '11988889999',
            'person_type' => 'PF',
            'city' => 'Campinas',
            'state' => 'SP',
        ]);

        $action = new CreateInsuredAction();
        $insured = $action->execute($dto);

        $this->assertInstanceOf(Insured::class, $insured);
        $this->assertEquals('Bruno Alencar', $insured->name);
        $this->assertEquals($lead->id, $insured->lead_id);
        $this->assertEquals(PersonTypeEnum::Individual, $insured->person_type);

        // O lead vinculado deve ser atualizado para Converted automaticamente
        $lead->refresh();
        $this->assertEquals(LeadStatusEnum::Converted, $lead->status);
    }

    public function test_update_insured_via_action(): void
    {
        $tenant = Tenant::create([
            'name' => 'Corretora Delta',
            'slug' => 'corretora-delta',
            'email' => 'delta@corretora.com',
            'document' => '55666777000188',
        ]);

        $insured = Insured::create([
            'tenant_id' => $tenant->id,
            'name' => 'Carla Dias',
            'email' => 'carla@dias.com',
            'city' => 'Santos',
            'state' => 'SP',
        ]);

        $updateDto = InsuredDTO::fromArray([
            'tenant_id' => $tenant->id,
            'name' => 'Carla Dias Novaes',
            'email' => 'carla.novaes@dias.com',
            'city' => 'São Paulo',
            'state' => 'SP',
            'notes' => 'Endereço atualizado.',
        ]);

        $action = new UpdateInsuredAction();
        $updated = $action->execute($insured, $updateDto);

        $this->assertEquals('Carla Dias Novaes', $updated->name);
        $this->assertEquals('carla.novaes@dias.com', $updated->email);
        $this->assertEquals('São Paulo', $updated->city);
        $this->assertEquals('Endereço atualizado.', $updated->notes);
    }

    public function test_delete_insured_via_action(): void
    {
        $tenant = Tenant::create([
            'name' => 'Corretora Epsilon',
            'slug' => 'corretora-epsilon',
            'email' => 'epsilon@corretora.com',
            'document' => '66777888000199',
        ]);

        $insured = Insured::create([
            'tenant_id' => $tenant->id,
            'name' => 'Danilo Freitas',
            'email' => 'danilo@freitas.com',
        ]);

        $action = new DeleteInsuredAction();
        $action->execute($insured);

        $this->assertSoftDeleted('insureds', [
            'id' => $insured->id,
        ]);
    }
}
