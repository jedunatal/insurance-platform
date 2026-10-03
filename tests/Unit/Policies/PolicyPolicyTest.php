<?php

namespace Tests\Unit\Policies;

use App\Models\Policy;
use App\Models\Tenant;
use App\Models\User;
use App\Policies\PolicyPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class PolicyPolicyTest extends TestCase
{
    use RefreshDatabase;

    private PolicyPolicy $policy;
    private Tenant $tenant1;
    private Tenant $tenant2;

    protected function setUp(): void
    {
        parent::setUp();
        $this->policy = new PolicyPolicy();

        Permission::firstOrCreate(['name' => 'view policies', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'create policies', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'update policies', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'delete policies', 'guard_name' => 'web']);

        $this->tenant1 = Tenant::create([
            'name' => 'Corretora 1',
            'slug' => 'corretora-1',
            'email' => 'c1@test.com',
            'document' => '11111111000101',
        ]);

        $this->tenant2 = Tenant::create([
            'name' => 'Corretora 2',
            'slug' => 'corretora-2',
            'email' => 'c2@test.com',
            'document' => '22222222000102',
        ]);
    }

    private function createUser(Tenant $tenant, array $permissions = []): User
    {
        $user = User::create([
            'tenant_id' => $tenant->id,
            'name' => 'User ' . uniqid(),
            'email' => uniqid() . '@test.com',
            'password' => bcrypt('secret'),
        ]);

        if (! empty($permissions)) {
            $user->givePermissionTo($permissions);
        }

        return $user;
    }

    public function test_view_any_checks_permission(): void
    {
        $userAllowed = $this->createUser($this->tenant1, ['view policies']);
        $this->assertTrue($this->policy->viewAny($userAllowed));

        $userDenied = $this->createUser($this->tenant1);
        $this->assertFalse($this->policy->viewAny($userDenied));
    }

    public function test_view_requires_both_permission_and_same_tenant(): void
    {
        $policyModel = new Policy();
        $policyModel->tenant_id = $this->tenant1->id;

        // Caso 1: Tem permissão e mesmo tenant
        $user1 = $this->createUser($this->tenant1, ['view policies']);
        $this->assertTrue($this->policy->view($user1, $policyModel));

        // Caso 2: Tem permissão mas tenant diferente
        $user2 = $this->createUser($this->tenant2, ['view policies']);
        $this->assertFalse($this->policy->view($user2, $policyModel));

        // Caso 3: Mesmo tenant mas sem permissão
        $user3 = $this->createUser($this->tenant1);
        $this->assertFalse($this->policy->view($user3, $policyModel));
    }

    public function test_create_checks_permission(): void
    {
        $userAllowed = $this->createUser($this->tenant1, ['create policies']);
        $this->assertTrue($this->policy->create($userAllowed));

        $userDenied = $this->createUser($this->tenant1);
        $this->assertFalse($this->policy->create($userDenied));
    }

    public function test_update_requires_permission_and_same_tenant(): void
    {
        $policyModel = new Policy();
        $policyModel->tenant_id = $this->tenant1->id;

        $userAuthorized = $this->createUser($this->tenant1, ['update policies']);
        $this->assertTrue($this->policy->update($userAuthorized, $policyModel));

        $userDifferentTenant = $this->createUser($this->tenant2, ['update policies']);
        $this->assertFalse($this->policy->update($userDifferentTenant, $policyModel));

        $userNoPerm = $this->createUser($this->tenant1);
        $this->assertFalse($this->policy->update($userNoPerm, $policyModel));
    }

    public function test_delete_requires_permission_and_same_tenant(): void
    {
        $policyModel = new Policy();
        $policyModel->tenant_id = $this->tenant1->id;

        $userAuthorized = $this->createUser($this->tenant1, ['delete policies']);
        $this->assertTrue($this->policy->delete($userAuthorized, $policyModel));

        $userDenied = $this->createUser($this->tenant1);
        $this->assertFalse($this->policy->delete($userDenied, $policyModel));

        $userWrongTenant = $this->createUser($this->tenant2, ['delete policies']);
        $this->assertFalse($this->policy->delete($userWrongTenant, $policyModel));
    }
}
