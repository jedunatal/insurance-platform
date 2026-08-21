<?php

namespace Tests\Feature;

use App\Enums\InsuranceBranchEnum;
use App\Enums\LeadStatusEnum;
use App\Enums\PolicyStatusEnum;
use App\Models\Insured;
use App\Models\Lead;
use App\Models\Policy;
use App\Models\Tenant;
use App\Services\CRM\DashboardService;
use App\Support\CurrencyHelper;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RenewalsPipelineTest extends TestCase
{
    use RefreshDatabase;

    private function createTenantAndInsured(): array
    {
        $tenant = Tenant::create([
            'name'     => 'Corretora de Teste',
            'slug'     => 'corretora-teste',
            'email'    => 'teste@corretora.com',
            'document' => '11222333000199',
        ]);

        $insured = Insured::create([
            'tenant_id' => $tenant->id,
            'name'      => 'Cliente Teste',
            'email'     => 'cliente@teste.com',
            'document'  => '123.456.789-00',
        ]);

        return [$tenant, $insured];
    }

    public function test_critical_renewals_query_filters_active_policies_expiring_within_30_days(): void
    {
        [$tenant, $insured] = $this->createTenantAndInsured();

        // 1. Apólice ativa que vence em 10 dias (DEVE APARECER)
        $expiringPolicy = Policy::create([
            'tenant_id'     => $tenant->id,
            'insured_id'    => $insured->id,
            'policy_number' => 'POL-EXPIRING-10D',
            'insurer'       => 'Porto Seguro',
            'branch'        => InsuranceBranchEnum::Auto->value,
            'status'        => PolicyStatusEnum::Active->value,
            'start_date'    => now()->subYear(),
            'end_date'      => now()->addDays(10),
            'total_premium' => 2500.00,
        ]);

        // 2. Apólice ativa que vence em 45 dias (NÃO DEVE APARECER no filtro de 30 dias)
        $farPolicy = Policy::create([
            'tenant_id'     => $tenant->id,
            'insured_id'    => $insured->id,
            'policy_number' => 'POL-FAR-45D',
            'insurer'       => 'Allianz',
            'branch'        => InsuranceBranchEnum::Auto->value,
            'status'        => PolicyStatusEnum::Active->value,
            'start_date'    => now()->subYear(),
            'end_date'      => now()->addDays(45),
            'total_premium' => 3000.00,
        ]);

        // 3. Apólice cancelada que venceria em 5 dias (NÃO DEVE APARECER)
        $cancelledPolicy = Policy::create([
            'tenant_id'     => $tenant->id,
            'insured_id'    => $insured->id,
            'policy_number' => 'POL-CANCELLED-5D',
            'insurer'       => 'Tokio Marine',
            'branch'        => InsuranceBranchEnum::Auto->value,
            'status'        => PolicyStatusEnum::Cancelled->value,
            'start_date'    => now()->subYear(),
            'end_date'      => now()->addDays(5),
            'total_premium' => 1800.00,
        ]);

        $service = app(DashboardService::class);
        $renewals = $service->getCriticalRenewals(days: 30, limit: 10, tenantId: $tenant->id);
        $count = $service->getCriticalRenewalsCount(days: 30, tenantId: $tenant->id);

        $this->assertEquals(1, $count);
        $this->assertCount(1, $renewals);
        $this->assertEquals($expiringPolicy->id, $renewals->first()->id);
        $this->assertFalse($renewals->contains('id', $farPolicy->id));
        $this->assertFalse($renewals->contains('id', $cancelledPolicy->id));
    }

    public function test_critical_renewals_handles_multiple_active_status_synonyms(): void
    {
        [$tenant, $insured] = $this->createTenantAndInsured();

        // Apólice gravada como 'Vigente'
        $policyVigente = Policy::create([
            'tenant_id'     => $tenant->id,
            'insured_id'    => $insured->id,
            'policy_number' => 'POL-VIGENTE-1',
            'status'        => 'Vigente',
            'start_date'    => now()->subYear(),
            'end_date'      => now()->addDays(15),
            'total_premium' => 2000.00,
        ]);

        // Apólice gravada como 'Ativa'
        $policyAtiva = Policy::create([
            'tenant_id'     => $tenant->id,
            'insured_id'    => $insured->id,
            'policy_number' => 'POL-ATIVA-1',
            'status'        => 'Ativa',
            'start_date'    => now()->subYear(),
            'end_date'      => now()->addDays(20),
            'total_premium' => 1500.00,
        ]);

        $service = app(DashboardService::class);
        $renewals = $service->getCriticalRenewals(days: 30, limit: 10, tenantId: $tenant->id);
        $count = $service->getCriticalRenewalsCount(days: 30, tenantId: $tenant->id);

        $this->assertEquals(2, $count);
        $this->assertCount(2, $renewals);
        $this->assertTrue($renewals->contains('id', $policyVigente->id));
        $this->assertTrue($renewals->contains('id', $policyAtiva->id));
    }

    public function test_lead_funnel_aggregates_correctly_with_status_cases(): void
    {
        [$tenant] = $this->createTenantAndInsured();

        Lead::create([
            'tenant_id' => $tenant->id,
            'name'      => 'Lead Novo',
            'status'    => 'Novo',
        ]);

        Lead::create([
            'tenant_id' => $tenant->id,
            'name'      => 'Lead Negociando',
            'status'    => 'Em Negociação',
        ]);

        Lead::create([
            'tenant_id' => $tenant->id,
            'name'      => 'Lead Convertido',
            'status'    => 'Convertido',
        ]);

        $service = app(DashboardService::class);
        $funnel = $service->getLeadFunnel($tenant->id);

        $this->assertIsArray($funnel);
        $novoCase = collect($funnel)->firstWhere('status', LeadStatusEnum::New->value);
        $this->assertEquals(1, $novoCase['count']);

        $negCase = collect($funnel)->firstWhere('status', LeadStatusEnum::InNegotiation->value);
        $this->assertEquals(1, $negCase['count']);

        $convCase = collect($funnel)->firstWhere('status', LeadStatusEnum::Converted->value);
        $this->assertEquals(1, $convCase['count']);
    }

    public function test_currency_helper_parses_brl_formats_correctly(): void
    {
        $this->assertEquals(1500.00, CurrencyHelper::parse('R$ 1.500,00'));
        $this->assertEquals(3250.50, CurrencyHelper::parse('3.250,50'));
        $this->assertEquals(1234567.89, CurrencyHelper::parse('R$ 1.234.567,89'));
        $this->assertEquals(100.50, CurrencyHelper::parse('100.50'));
        $this->assertEquals(0.00, CurrencyHelper::parse(''));
        $this->assertEquals(0.00, CurrencyHelper::parse(null));

        $this->assertEquals('1500.00', CurrencyHelper::toDecimalString('R$ 1.500,00'));
        $this->assertEquals('3250.50', CurrencyHelper::toDecimalString('3.250,50'));
    }

    public function test_policy_model_mutators_convert_brl_strings_to_decimals(): void
    {
        [$tenant, $insured] = $this->createTenantAndInsured();

        $policy = new Policy();
        $policy->tenant_id = $tenant->id;
        $policy->insured_id = $insured->id;
        $policy->policy_number = 'POL-CURRENCY-TEST';
        $policy->status = PolicyStatusEnum::Active;
        $policy->net_premium = 'R$ 2.500,50';
        $policy->iof_amount = '184,54';
        $policy->total_premium = 'R$ 2.685,04';
        $policy->deductible_amount = '1.500,00';
        $policy->save();

        $fresh = $policy->fresh();
        $this->assertEquals(2500.50, (float) $fresh->net_premium);
        $this->assertEquals(184.54, (float) $fresh->iof_amount);
        $this->assertEquals(2685.04, (float) $fresh->total_premium);
        $this->assertEquals(1500.00, (float) $fresh->deductible_amount);
    }
}
