<?php

namespace Tests\Feature;

use App\Actions\Lead\CreateLeadAction;
use App\Actions\Lead\DeleteLeadAction;
use App\Actions\Lead\UpdateLeadAction;
use App\DTOs\LeadDTO;
use App\Enums\LeadSourceEnum;
use App\Enums\LeadStatusEnum;
use App\Models\Lead;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LeadActionsTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_lead_via_action(): void
    {
        $tenant = Tenant::create([
            'name' => 'Corretora Seguro Total',
            'slug' => 'seguro-total',
            'email' => 'contato@segurototal.com',
            'document' => '11222333000144',
        ]);

        $user = User::create([
            'tenant_id' => $tenant->id,
            'name' => 'Corretor Geral',
            'email' => 'corretor@segurototal.com',
            'password' => bcrypt('password'),
        ]);

        $dto = LeadDTO::fromArray([
            'tenant_id' => $tenant->id,
            'created_by' => $user->id,
            'name' => 'Lucas Peixoto',
            'email' => 'lucas@peixoto.com',
            'phone' => '11977776666',
            'source' => LeadSourceEnum::Site->value,
            'status' => LeadStatusEnum::New->value,
            'notes' => 'Primeiro contato.',
        ]);

        $action = new CreateLeadAction();
        $lead = $action->execute($dto);

        $this->assertInstanceOf(Lead::class, $lead);
        $this->assertEquals('Lucas Peixoto', $lead->name);
        $this->assertEquals('lucas@peixoto.com', $lead->email);
        $this->assertEquals(LeadStatusEnum::New, $lead->status);
        $this->assertDatabaseHas('leads', [
            'id' => $lead->id,
            'name' => 'Lucas Peixoto',
            'email' => 'lucas@peixoto.com',
        ]);
    }

    public function test_can_update_lead_via_action(): void
    {
        $tenant = Tenant::create([
            'name' => 'Corretora Alpha',
            'slug' => 'corretora-alpha',
            'email' => 'alpha@corretora.com',
            'document' => '22333444000155',
        ]);

        $user = User::create([
            'tenant_id' => $tenant->id,
            'name' => 'Corretor Alpha',
            'email' => 'corretor@alpha.com',
            'password' => bcrypt('password'),
        ]);

        $lead = Lead::create([
            'tenant_id' => $tenant->id,
            'created_by' => $user->id,
            'name' => 'Fernanda Lima',
            'email' => 'fernanda@lima.com',
            'source' => LeadSourceEnum::Whatsapp->value,
            'status' => LeadStatusEnum::New->value,
        ]);

        $updateDto = LeadDTO::fromArray([
            'tenant_id' => $tenant->id,
            'created_by' => $user->id,
            'name' => 'Fernanda Lima Santos',
            'email' => 'fernanda.santos@lima.com',
            'source' => LeadSourceEnum::Whatsapp->value,
            'status' => LeadStatusEnum::InNegotiation->value,
            'notes' => 'Proposta enviada.',
        ]);

        $action = new UpdateLeadAction();
        $updated = $action->execute($lead, $updateDto);

        $this->assertEquals('Fernanda Lima Santos', $updated->name);
        $this->assertEquals('fernanda.santos@lima.com', $updated->email);
        $this->assertEquals(LeadStatusEnum::InNegotiation, $updated->status);
        $this->assertEquals('Proposta enviada.', $updated->notes);
    }

    public function test_can_delete_lead_via_action(): void
    {
        $tenant = Tenant::create([
            'name' => 'Corretora Beta',
            'slug' => 'corretora-beta-lead',
            'email' => 'beta@lead.com',
            'document' => '33444555000166',
        ]);

        $user = User::create([
            'tenant_id' => $tenant->id,
            'name' => 'Corretor Beta',
            'email' => 'corretor@beta.com',
            'password' => bcrypt('password'),
        ]);

        $lead = Lead::create([
            'tenant_id' => $tenant->id,
            'created_by' => $user->id,
            'name' => 'Leandro Castro',
            'email' => 'leandro@castro.com',
            'source' => LeadSourceEnum::Site->value,
            'status' => LeadStatusEnum::New->value,
        ]);

        $action = new DeleteLeadAction();
        $action->execute($lead);

        $this->assertSoftDeleted('leads', [
            'id' => $lead->id,
        ]);
    }
}
