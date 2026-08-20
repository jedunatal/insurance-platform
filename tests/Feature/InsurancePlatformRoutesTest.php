<?php

namespace Tests\Feature;

use App\Models\Claim;
use App\Models\Insured;
use App\Models\Lead;
use App\Models\Policy;
use App\Models\Tenant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InsurancePlatformRoutesTest extends TestCase
{
    use RefreshDatabase;

    public function test_all_platform_routes_are_accessible(): void
    {
        $tenant = Tenant::create([
            'name' => 'Empresa Padrão',
            'slug' => 'empresa-padrao',
            'email' => 'contato@empresa.com',
            'document' => '00000000000191',
        ]);

        $lead = Lead::create([
            'tenant_id' => $tenant->id,
            'name' => 'Lead de Teste',
            'email' => 'lead@teste.com',
            'phone' => '11999998888',
        ]);

        $insured = Insured::create([
            'tenant_id' => $tenant->id,
            'lead_id' => $lead->id,
            'name' => 'Segurado de Teste',
            'email' => 'segurado@teste.com',
            'phone' => '11999998888',
            'document' => '12345678901',
        ]);

        $policy = Policy::create([
            'tenant_id' => $tenant->id,
            'insured_id' => $insured->id,
            'policy_number' => 'POL-TEST-001',
            'insurer' => 'Porto Seguro',
            'branch' => 'Automóvel',
            'status' => 'active',
            'start_date' => now(),
            'end_date' => now()->addYear(),
            'total_premium' => 2500.00,
        ]);

        $claim = Claim::create([
            'tenant_id' => $tenant->id,
            'policy_id' => $policy->id,
            'insured_id' => $insured->id,
            'claim_number' => 'SIN-TEST-001',
            'occurrence_date' => now()->subDays(2),
            'report_date' => now(),
            'occurrence_description' => 'Sinistro teste',
            'status' => 'reported',
        ]);

        // Dashboard
        $this->get(route('dashboard'))->assertStatus(200);

        // Leads
        $this->get(route('leads.index'))->assertStatus(200);
        $this->get(route('leads.create'))->assertStatus(200);
        $this->get(route('leads.view', $lead))->assertStatus(200);
        $this->get(route('leads.edit', $lead))->assertStatus(200);

        // Insureds
        $this->get(route('insureds.index'))->assertStatus(200);
        $this->get(route('insureds.create'))->assertStatus(200);
        $this->get(route('insureds.create', ['lead_id' => $lead->id]))->assertStatus(200);
        $this->get(route('insureds.view', $insured))->assertStatus(200);
        $this->get(route('insureds.edit', $insured))->assertStatus(200);

        // Policies
        $this->get(route('policies.index'))->assertStatus(200);
        $this->get(route('policies.create'))->assertStatus(200);
        $this->get(route('policies.view', $policy))->assertStatus(200);
        $this->get(route('policies.edit', $policy))->assertStatus(200);

        // Claims
        $this->get(route('claims.index'))->assertStatus(200);
        $this->get(route('claims.create'))->assertStatus(200);
        $this->get(route('claims.create', ['policy_id' => $policy->id]))->assertStatus(200);
        $this->get(route('claims.view', $claim))->assertStatus(200);
        $this->get(route('claims.edit', $claim))->assertStatus(200);
    }
}
