<?php

namespace Tests\Feature;

use App\Actions\Policy\CreatePolicyAction;
use App\Actions\Policy\DeletePolicyAction;
use App\Actions\Policy\UpdatePolicyAction;
use App\DTOs\PolicyData;
use App\Enums\InsuranceBranchEnum;
use App\Enums\PolicyStatusEnum;
use App\Models\Insured;
use App\Models\Policy;
use App\Models\PolicyInstallment;
use App\Models\Tenant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PolicyActionsTest extends TestCase
{
    use RefreshDatabase;

    public function test_update_policy_action_recalculates_installments_when_premium_changes(): void
    {
        $tenant = Tenant::create([
            'name' => 'Corretora Vanguarda',
            'slug' => 'corretora-vanguarda',
            'email' => 'vanguarda@corretora.com',
            'document' => '77888999000100',
        ]);

        $insured = Insured::create([
            'tenant_id' => $tenant->id,
            'name' => 'Eduardo Paes',
            'email' => 'eduardo@paes.com',
        ]);

        $createDto = PolicyData::fromArray([
            'tenant_id' => $tenant->id,
            'insured_id' => $insured->id,
            'policy_number' => 'POL-VANG-001',
            'insurer' => 'Porto Seguro',
            'branch' => InsuranceBranchEnum::Auto->value,
            'status' => PolicyStatusEnum::Active->value,
            'net_premium' => 1000.00,
            'total_premium' => 1073.80,
            'commission_amount' => 150.00,
            'installments_count' => 2,
        ]);

        $createAction = app(CreatePolicyAction::class);
        $policy = $createAction->execute($createDto);

        $this->assertEquals(2, $policy->installments()->count());
        $this->assertEquals(1073.80, (float) $policy->installments()->sum('gross_amount'));

        // Atualização do total premium e quantidade de parcelas
        $updateDto = PolicyData::fromArray([
            'tenant_id' => $tenant->id,
            'insured_id' => $insured->id,
            'policy_number' => 'POL-VANG-001',
            'insurer' => 'Porto Seguro',
            'branch' => InsuranceBranchEnum::Auto->value,
            'status' => PolicyStatusEnum::Active->value,
            'net_premium' => 2000.00,
            'total_premium' => 2147.60,
            'commission_amount' => 300.00,
            'installments_count' => 4,
        ]);

        $updateAction = app(UpdatePolicyAction::class);
        $updated = $updateAction->execute($policy, $updateDto);

        $this->assertEquals(4, $updated->installments()->count());
        $this->assertEquals(2147.60, (float) $updated->installments()->sum('gross_amount'));
    }

    public function test_delete_policy_action(): void
    {
        $tenant = Tenant::create([
            'name' => 'Corretora Delete Test',
            'slug' => 'corretora-delete-test',
            'email' => 'delete@corretora.com',
            'document' => '88999000000111',
        ]);

        $insured = Insured::create([
            'tenant_id' => $tenant->id,
            'name' => 'Gabriel Ramos',
            'email' => 'gabriel@ramos.com',
        ]);

        $policy = Policy::create([
            'tenant_id' => $tenant->id,
            'insured_id' => $insured->id,
            'policy_number' => 'POL-DEL-001',
            'insurer' => 'Azul Seguros',
            'branch' => InsuranceBranchEnum::Home->value,
            'status' => PolicyStatusEnum::Draft->value,
        ]);

        $action = new DeletePolicyAction();
        $action->execute($policy);

        $this->assertSoftDeleted('policies', [
            'id' => $policy->id,
        ]);
    }
}
