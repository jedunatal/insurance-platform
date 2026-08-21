<?php

namespace Tests\Feature;

use App\Actions\Financial\GeneratePolicyInstallmentsAction;
use App\Actions\Financial\SettleInstallmentAction;
use App\Actions\Policy\CreatePolicyAction;
use App\DTOs\PolicyData;
use App\Enums\FinancialStatusEnum;
use App\Enums\InsuranceBranchEnum;
use App\Enums\PaymentMethodEnum;
use App\Enums\PolicyStatusEnum;
use App\Livewire\Financial\ListInstallments;
use App\Models\Insured;
use App\Models\Policy;
use App\Models\PolicyInstallment;
use App\Models\Tenant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class FinancialModuleTest extends TestCase
{
    use RefreshDatabase;

    public function test_insurance_branch_default_iof_rates(): void
    {
        $this->assertEquals(7.38, InsuranceBranchEnum::Auto->defaultIofRate());
        $this->assertEquals(7.38, InsuranceBranchEnum::Home->defaultIofRate());
        $this->assertEquals(7.38, InsuranceBranchEnum::Business->defaultIofRate());
        $this->assertEquals(0.38, InsuranceBranchEnum::Life->defaultIofRate());
        $this->assertEquals(0.38, InsuranceBranchEnum::Health->defaultIofRate());
        $this->assertEquals(0.00, InsuranceBranchEnum::Rural->defaultIofRate());
        $this->assertEquals(7.38, InsuranceBranchEnum::Other->defaultIofRate());
    }

    public function test_generate_policy_installments_action_creates_installments_and_commissions(): void
    {
        $tenant = Tenant::create([
            'name' => 'Corretora Alfa',
            'slug' => 'corretora-alfa',
            'email' => 'alfa@corretora.com',
            'document' => '00000000000191',
        ]);

        $insured = Insured::create([
            'tenant_id' => $tenant->id,
            'name' => 'Roberto Carlos',
            'email' => 'roberto@email.com',
        ]);

        $policy = Policy::create([
            'tenant_id'             => $tenant->id,
            'insured_id'            => $insured->id,
            'policy_number'         => 'POL-FIN-001',
            'insurer'               => 'Porto Seguro',
            'branch'                => InsuranceBranchEnum::Auto->value,
            'status'                => PolicyStatusEnum::Active->value,
            'start_date'            => now()->startOfDay(),
            'end_date'              => now()->addYear(),
            'net_premium'           => 3000.00,
            'iof_rate'              => 7.38,
            'iof_amount'            => 221.40,
            'total_premium'         => 3221.40,
            'commission_percentage' => 15.00,
            'commission_amount'     => 450.00,
            'payment_method'        => PaymentMethodEnum::Invoice->value,
            'installments_count'    => 3,
        ]);

        $action = new GeneratePolicyInstallmentsAction();
        $installments = $action->execute($policy);

        $this->assertCount(3, $installments);
        $this->assertEquals(3, PolicyInstallment::count());

        $first = $installments->first();
        $this->assertEquals(1, $first->installment_number);
        $this->assertEquals(3, $first->total_installments);
        $this->assertEquals(FinancialStatusEnum::Pending, $first->status);
        $this->assertNotNull($first->due_date);

        // Soma total dos prêmios das parcelas deve ser igual ao total_premium
        $sumGross = PolicyInstallment::where('policy_id', $policy->id)->sum('gross_amount');
        $this->assertEquals(3221.40, round((float) $sumGross, 2));

        // Soma total das comissões esperadas deve ser igual ao commission_amount
        $sumComm = PolicyInstallment::where('policy_id', $policy->id)->sum('commission_expected');
        $this->assertEquals(450.00, round((float) $sumComm, 2));
    }

    public function test_settle_installment_action_records_payment_and_commission_received(): void
    {
        $tenant = Tenant::create([
            'name' => 'Corretora Beta',
            'slug' => 'corretora-beta',
            'email' => 'beta@corretora.com',
            'document' => '00000000000192',
        ]);

        $insured = Insured::create([
            'tenant_id' => $tenant->id,
            'name' => 'Maria Silva',
            'email' => 'maria@email.com',
        ]);

        $policy = Policy::create([
            'tenant_id'          => $tenant->id,
            'insured_id'         => $insured->id,
            'policy_number'      => 'POL-FIN-002',
            'status'             => PolicyStatusEnum::Active->value,
            'total_premium'      => 1000.00,
            'commission_amount'  => 100.00,
            'installments_count' => 1,
        ]);

        $installment = PolicyInstallment::create([
            'tenant_id'           => $tenant->id,
            'policy_id'           => $policy->id,
            'insured_id'          => $insured->id,
            'installment_number'  => 1,
            'total_installments'  => 1,
            'due_date'            => now()->toDateString(),
            'gross_amount'        => 1000.00,
            'commission_expected' => 100.00,
            'commission_received' => null,
            'status'              => FinancialStatusEnum::Pending,
        ]);

        $action = new SettleInstallmentAction();
        $settled = $action->execute(
            installment: $installment,
            paymentDate: now(),
            commissionReceived: 100.00,
            notes: 'Comissão creditada em conta corrente'
        );

        $this->assertEquals(FinancialStatusEnum::Paid, $settled->status);
        $this->assertEquals(100.00, (float) $settled->commission_received);
        $this->assertNotNull($settled->payment_date);
        $this->assertEquals('Comissão creditada em conta corrente', $settled->notes);
    }

    public function test_create_policy_action_automatically_generates_installments(): void
    {
        $tenant = Tenant::create([
            'name' => 'Corretora Gama',
            'slug' => 'corretora-gama',
            'email' => 'gama@corretora.com',
            'document' => '00000000000193',
        ]);

        $insured = Insured::create([
            'tenant_id' => $tenant->id,
            'name' => 'Fernanda Lima',
            'email' => 'fernanda@email.com',
        ]);

        $dto = PolicyData::fromArray([
            'tenant_id'             => $tenant->id,
            'insured_id'            => $insured->id,
            'policy_number'         => 'POL-AUTO-INSTALL',
            'insurer'               => 'Allianz Seguros',
            'branch'                => InsuranceBranchEnum::Auto->value,
            'status'                => PolicyStatusEnum::Active->value,
            'start_date'            => now()->toDateTimeString(),
            'end_date'              => now()->addYear()->toDateTimeString(),
            'net_premium'           => 2000.00,
            'iof_rate'              => 7.38,
            'iof_amount'            => 147.60,
            'total_premium'         => 2147.60,
            'commission_percentage' => 10.00,
            'commission_amount'     => 200.00,
            'payment_method'        => PaymentMethodEnum::Invoice->value,
            'installments_count'    => 4,
        ]);

        $action = new CreatePolicyAction();
        $policy = $action->execute($dto);

        $this->assertInstanceOf(Policy::class, $policy);
        $this->assertCount(4, $policy->installments);
        $this->assertEquals(2147.60, (float) PolicyInstallment::where('policy_id', $policy->id)->sum('gross_amount'));
    }

    public function test_financial_list_page_returns_http_200_and_renders_livewire_table(): void
    {
        $this->authenticateUser();

        $response = $this->get(route('financial.index'));
        $response->assertStatus(200);
        $response->assertSee('Gestão Financeira & Comissões');
        $response->assertSee('Comissões a Receber');
        $response->assertSee('Comissões Recebidas');

        Livewire::test(ListInstallments::class)
            ->assertStatus(200)
            ->assertViewHas('metrics');
    }
}
