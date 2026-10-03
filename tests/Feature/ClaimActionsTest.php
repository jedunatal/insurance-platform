<?php

namespace Tests\Feature;

use App\Actions\Claim\CreateClaimAction;
use App\Actions\Claim\DeleteClaimAction;
use App\Actions\Claim\UpdateClaimAction;
use App\DTOs\ClaimData;
use App\Enums\ClaimStatusEnum;
use App\Enums\ClaimTypeEnum;
use App\Models\Claim;
use App\Models\Insured;
use App\Models\Policy;
use App\Models\Tenant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClaimActionsTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_update_claim_status_and_indemnified_amount(): void
    {
        $tenant = Tenant::create([
            'name' => 'Corretora Seguro Max',
            'slug' => 'seguro-max',
            'email' => 'max@seguro.com',
            'document' => '99000111000122',
        ]);

        $insured = Insured::create([
            'tenant_id' => $tenant->id,
            'name' => 'Helena Vieira',
            'email' => 'helena@vieira.com',
        ]);

        $policy = Policy::create([
            'tenant_id' => $tenant->id,
            'insured_id' => $insured->id,
            'policy_number' => 'POL-MAX-001',
        ]);

        $createDto = ClaimData::fromArray([
            'tenant_id' => $tenant->id,
            'policy_id' => $policy->id,
            'insured_id' => $insured->id,
            'claim_number' => 'SIN-MAX-001',
            'protocol_number' => 'PROT-001',
            'claim_type' => ClaimTypeEnum::Collision->value,
            'status' => ClaimStatusEnum::Reported->value,
            'occurrence_date' => '2026-08-01 10:00:00',
            'estimated_amount' => 5000.00,
            'occurrence_description' => 'Batida leve.',
        ]);

        $createAction = new CreateClaimAction();
        $claim = $createAction->execute($createDto);

        $this->assertEquals(ClaimStatusEnum::Reported, $claim->status);

        $updateDto = ClaimData::fromArray([
            'tenant_id' => $tenant->id,
            'policy_id' => $policy->id,
            'insured_id' => $insured->id,
            'claim_number' => 'SIN-MAX-001',
            'protocol_number' => 'PROT-001',
            'claim_type' => ClaimTypeEnum::Collision->value,
            'status' => ClaimStatusEnum::Indemnified->value,
            'occurrence_date' => '2026-08-01 10:00:00',
            'estimated_amount' => 5000.00,
            'indemnified_amount' => 4800.00,
            'occurrence_description' => 'Batida leve - indenização paga.',
        ]);

        $updateAction = new UpdateClaimAction();
        $updated = $updateAction->execute($claim, $updateDto);

        $this->assertEquals(ClaimStatusEnum::Indemnified, $updated->status);
        $this->assertEquals(4800.00, (float) $updated->indemnified_amount);
    }

    public function test_can_delete_claim(): void
    {
        $tenant = Tenant::create([
            'name' => 'Corretora Sinistro Delete',
            'slug' => 'corretora-sinistro-del',
            'email' => 'sinistro@del.com',
            'document' => '10203040000133',
        ]);

        $insured = Insured::create([
            'tenant_id' => $tenant->id,
            'name' => 'Igor Santos',
            'email' => 'igor@santos.com',
        ]);

        $policy = Policy::create([
            'tenant_id' => $tenant->id,
            'insured_id' => $insured->id,
            'policy_number' => 'POL-DEL-CLAIM',
        ]);

        $claim = Claim::create([
            'tenant_id' => $tenant->id,
            'policy_id' => $policy->id,
            'insured_id' => $insured->id,
            'claim_number' => 'SIN-DEL-001',
            'occurrence_date' => '2026-08-05 08:00:00',
            'report_date' => '2026-08-05 09:00:00',
            'occurrence_description' => 'Dano em retrovisor.',
        ]);

        $deleteAction = new DeleteClaimAction();
        $deleteAction->execute($claim);

        $this->assertSoftDeleted('claims', [
            'id' => $claim->id,
        ]);
    }
}
