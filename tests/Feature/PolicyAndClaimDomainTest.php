<?php

namespace Tests\Feature;

use App\Actions\Claim\CreateClaimAction;
use App\Actions\Policy\CreatePolicyAction;
use App\DTOs\ClaimData;
use App\DTOs\PolicyData;
use App\Enums\ClaimStatusEnum;
use App\Enums\ClaimTypeEnum;
use App\Enums\InsuranceBranchEnum;
use App\Enums\PolicyPaymentMethodEnum;
use App\Enums\PolicyStatusEnum;
use App\Models\Claim;
use App\Models\Insured;
use App\Models\Policy;
use App\Models\Tenant;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PolicyAndClaimDomainTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_policy_with_coverages_and_deductible(): void
    {
        $tenant = Tenant::create([
            'name' => 'Corretora Elite',
            'slug' => 'corretora-elite',
            'email' => 'contato@elite.com',
            'document' => '99888777000100',
        ]);

        $insured = Insured::create([
            'tenant_id' => $tenant->id,
            'name' => 'Mariana Silveira',
            'email' => 'mariana@silveira.com',
            'phone' => '21999998888',
            'document' => '98765432100',
        ]);

        $coverages = [
            ['name' => 'Colisão e Incêndio', 'limit_amount' => '100% FIPE', 'deductible' => 'R$ 3.000,00'],
            ['name' => 'Danos Materiais a Terceiros', 'limit_amount' => 'R$ 150.000,00', 'deductible' => 'Isenta'],
            ['name' => 'Vidros e Faróis', 'limit_amount' => 'Completa', 'deductible' => 'R$ 250,00'],
        ];

        $policyDto = PolicyData::fromArray([
            'tenant_id' => $tenant->id,
            'insured_id' => $insured->id,
            'policy_number' => 'POL-2026-9901',
            'proposal_number' => 'PROP-2026-9901',
            'insurer' => 'Porto Seguro',
            'branch' => InsuranceBranchEnum::Auto->value,
            'status' => PolicyStatusEnum::Active->value,
            'start_date' => '2026-01-01 00:00:00',
            'end_date' => '2027-01-01 00:00:00',
            'net_premium' => 3500.00,
            'iof_amount' => 258.30,
            'total_premium' => 3758.30,
            'deductible_amount' => 3000.00,
            'payment_method' => PolicyPaymentMethodEnum::CreditCard->value,
            'installments_count' => 10,
            'coverages' => $coverages,
            'notes' => 'Apólice emitida com bônus classe 5.',
        ]);

        $policyAction = app(CreatePolicyAction::class);
        $policy = $policyAction->execute($policyDto);

        $this->assertInstanceOf(Policy::class, $policy);
        $this->assertEquals('POL-2026-9901', $policy->policy_number);
        $this->assertEquals('Porto Seguro', $policy->insurer);
        $this->assertEquals('Automóvel', $policy->branch);
        $this->assertEquals(PolicyStatusEnum::Active, $policy->status);
        $this->assertEquals('3758.30', (string) $policy->total_premium);
        $this->assertEquals('3000.00', (string) $policy->deductible_amount);
        $this->assertCount(3, $policy->coverages);
        $this->assertEquals('Colisão e Incêndio', $policy->coverages[0]['name']);

        // Relacionamento Insured -> Policy
        $this->assertTrue($policy->insured->is($insured));
        $this->assertCount(1, $insured->policies);

        // Teste de criação de Sinistro vinculado à apólice
        $claimDto = ClaimData::fromArray([
            'tenant_id' => $tenant->id,
            'policy_id' => $policy->id,
            'insured_id' => $insured->id,
            'claim_number' => 'SIN-2026-0001',
            'protocol_number' => 'PROT-PS-887766',
            'claim_type' => ClaimTypeEnum::Collision->value,
            'status' => ClaimStatusEnum::Reported->value,
            'occurrence_date' => '2026-05-10 14:30:00',
            'report_date' => '2026-05-10 16:00:00',
            'location' => 'Av. Brasil, Rio de Janeiro / RJ',
            'occurrence_description' => 'Colisão traseira no semáforo.',
            'estimated_amount' => 4500.00,
            'deductible_amount' => 3000.00,
            'indemnified_amount' => 0.00,
        ]);

        $claimAction = app(CreateClaimAction::class);
        $claim = $claimAction->execute($claimDto);

        $this->assertInstanceOf(Claim::class, $claim);
        $this->assertEquals('SIN-2026-0001', $claim->claim_number);
        $this->assertEquals('PROT-PS-887766', $claim->protocol_number);
        $this->assertEquals(ClaimTypeEnum::Collision, $claim->claim_type);
        $this->assertEquals(ClaimStatusEnum::Reported, $claim->status);
        $this->assertEquals('4500.00', (string) $claim->estimated_amount);
        $this->assertEquals('3000.00', (string) $claim->deductible_amount);

        // Relacionamentos Eloquent
        $this->assertTrue($claim->policy->is($policy));
        $this->assertTrue($claim->insured->is($insured));
        $this->assertCount(1, $policy->claims);
        $this->assertCount(1, $insured->claims);
    }
}
