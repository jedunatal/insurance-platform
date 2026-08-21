<?php

namespace Tests\Feature;

use App\Enums\ClaimStatusEnum;
use App\Enums\ClaimTypeEnum;
use App\Enums\InsuranceBranchEnum;
use App\Enums\LeadStatusEnum;
use App\Enums\PolicyStatusEnum;
use App\Livewire\Dashboard\Overview;
use App\Models\Claim;
use App\Models\Insured;
use App\Models\Lead;
use App\Models\Policy;
use App\Models\Tenant;
use App\Services\CRM\DashboardService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_service_handles_empty_database_without_division_by_zero(): void
    {
        $service = app(DashboardService::class);

        $metrics = $service->getMetrics();

        $this->assertEquals(0, $metrics['month_leads']);
        $this->assertEquals(0, $metrics['total_leads']);
        $this->assertEquals(0, $metrics['converted_leads']);
        $this->assertSame(0.0, $metrics['conversion_rate']);
        $this->assertEquals(0, $metrics['active_insureds']);
        $this->assertEquals(0, $metrics['active_policies']);
        $this->assertSame(0.0, $metrics['total_active_premium']);
        $this->assertEquals(0, $metrics['open_claims']);
        $this->assertSame(0.0, $metrics['estimated_loss']);
        $this->assertSame(0.0, $metrics['total_indemnified']);
        $this->assertSame(0.0, $metrics['loss_ratio']);

        $branches = $service->getBranchDistribution();
        $this->assertIsArray($branches);
        $this->assertEmpty($branches);

        $insurers = $service->getInsurerDistribution();
        $this->assertIsArray($insurers);
        $this->assertEmpty($insurers);

        $funnel = $service->getLeadFunnel();
        $this->assertIsArray($funnel);
        $this->assertNotEmpty($funnel);
        $this->assertEquals(0, $funnel[0]['count']);
        $this->assertSame(0.0, $funnel[0]['percentage']);

        $criticalRenewals = $service->getCriticalRenewals(30);
        $this->assertCount(0, $criticalRenewals);

        $criticalRenewalsCount = $service->getCriticalRenewalsCount(30);
        $this->assertEquals(0, $criticalRenewalsCount);
    }

    public function test_service_computes_kpis_and_distributions_correctly_with_data(): void
    {
        $tenant = Tenant::create([
            'name' => 'Empresa Teste',
            'slug' => 'empresa-teste',
            'email' => 'teste@empresa.com',
            'document' => '00000000000191',
        ]);

        // Leads: 2 Convertidos e 2 Novos (Total 4 -> 50% conversão)
        Lead::create([
            'tenant_id' => $tenant->id,
            'name' => 'Lead 1',
            'email' => 'lead1@teste.com',
            'status' => LeadStatusEnum::Converted->value,
        ]);
        Lead::create([
            'tenant_id' => $tenant->id,
            'name' => 'Lead 2',
            'email' => 'lead2@teste.com',
            'status' => LeadStatusEnum::Converted->value,
        ]);
        Lead::create([
            'tenant_id' => $tenant->id,
            'name' => 'Lead 3',
            'email' => 'lead3@teste.com',
            'status' => LeadStatusEnum::New->value,
        ]);
        Lead::create([
            'tenant_id' => $tenant->id,
            'name' => 'Lead 4',
            'email' => 'lead4@teste.com',
            'status' => LeadStatusEnum::New->value,
        ]);

        // Segurados
        $insured1 = Insured::create([
            'tenant_id' => $tenant->id,
            'name' => 'Segurado Alfa',
            'email' => 'alfa@segurado.com',
        ]);
        $insured2 = Insured::create([
            'tenant_id' => $tenant->id,
            'name' => 'Segurado Beta',
            'email' => 'beta@segurado.com',
        ]);

        // Apólices:
        // Apólice 1: Automóvel, Porto Seguro, R$ 4.000,00, vence em 10 dias (renovação crítica)
        $policy1 = Policy::create([
            'tenant_id' => $tenant->id,
            'insured_id' => $insured1->id,
            'policy_number' => 'POL-001',
            'insurer' => 'Porto Seguro',
            'branch' => InsuranceBranchEnum::Auto->value,
            'status' => PolicyStatusEnum::Active->value,
            'start_date' => now()->subMonths(11),
            'end_date' => now()->addDays(10),
            'total_premium' => 4000.00,
        ]);

        // Apólice 2: Vida, SulAmérica, R$ 6.000,00, vence em 60 dias (fora dos 30 dias)
        Policy::create([
            'tenant_id' => $tenant->id,
            'insured_id' => $insured2->id,
            'policy_number' => 'POL-002',
            'insurer' => 'SulAmérica',
            'branch' => InsuranceBranchEnum::Life->value,
            'status' => PolicyStatusEnum::Active->value,
            'start_date' => now()->subMonths(6),
            'end_date' => now()->addDays(60),
            'total_premium' => 6000.00,
        ]);

        // Sinistro: 1 Aberto (R$ 2.500 est) e 1 Indenizado (R$ 1.000 pago)
        Claim::create([
            'tenant_id' => $tenant->id,
            'policy_id' => $policy1->id,
            'insured_id' => $insured1->id,
            'claim_number' => 'SIN-001',
            'protocol_number' => 'PROT-001',
            'claim_type' => ClaimTypeEnum::Collision->value,
            'status' => ClaimStatusEnum::Reported->value,
            'occurrence_date' => now()->subDays(3),
            'report_date' => now()->subDays(2),
            'estimated_amount' => 2500.00,
            'indemnified_amount' => 0.00,
            'occurrence_description' => 'Colisão leve',
        ]);

        Claim::create([
            'tenant_id' => $tenant->id,
            'policy_id' => $policy1->id,
            'insured_id' => $insured1->id,
            'claim_number' => 'SIN-002',
            'protocol_number' => 'PROT-002',
            'claim_type' => ClaimTypeEnum::Glass->value,
            'status' => ClaimStatusEnum::Indemnified->value,
            'occurrence_date' => now()->subMonths(2),
            'report_date' => now()->subMonths(2),
            'estimated_amount' => 1000.00,
            'indemnified_amount' => 1000.00,
            'occurrence_description' => 'Troca de parabrisa',
        ]);

        $service = app(DashboardService::class);
        $metrics = $service->getMetrics();

        // 4 leads, 2 convertidos = 50.0%
        $this->assertEquals(4, $metrics['total_leads']);
        $this->assertEquals(2, $metrics['converted_leads']);
        $this->assertEquals(50.0, $metrics['conversion_rate']);

        // 2 segurados ativos
        $this->assertEquals(2, $metrics['active_insureds']);

        // 2 apólices ativas somando R$ 10.000,00
        $this->assertEquals(2, $metrics['active_policies']);
        $this->assertEquals(10000.00, $metrics['total_active_premium']);

        // 1 sinistro aberto com R$ 2.500,00 estimado
        $this->assertEquals(1, $metrics['open_claims']);
        $this->assertEquals(2500.00, $metrics['estimated_loss']);

        // Renovações críticas (nos próximos 30 dias) -> Deve encontrar apenas a POL-001 (10 dias)
        $criticalRenewals = $service->getCriticalRenewals(30);
        $this->assertCount(1, $criticalRenewals);
        $this->assertEquals('POL-001', $criticalRenewals->first()->policy_number);
        $this->assertEquals(1, $service->getCriticalRenewalsCount(30));

        // Distribuição por Ramos
        $branchDist = $service->getBranchDistribution();
        $this->assertCount(2, $branchDist);

        // Distribuição por Seguradoras
        $insurerDist = $service->getInsurerDistribution();
        $this->assertCount(2, $insurerDist);
    }

    public function test_dashboard_route_returns_http_200_and_renders_livewire_component(): void
    {
        $this->authenticateUser();

        $response = $this->get(route('dashboard'));
        $response->assertStatus(200);
        $response->assertSee('Dashboard Executivo');
        $response->assertSee('Leads do Mês');
        $response->assertSee('Segurados Ativos');
        $response->assertSee('Prêmio Total Ativo');
        $response->assertSee('Sinistros Abertos');

        // Testa o componente Livewire isoladamente
        Livewire::test(Overview::class)
            ->assertStatus(200)
            ->assertViewHas('metrics')
            ->assertViewHas('criticalRenewals')
            ->assertViewHas('branchDistribution')
            ->assertViewHas('insurerDistribution')
            ->assertViewHas('leadFunnel');
    }
}
