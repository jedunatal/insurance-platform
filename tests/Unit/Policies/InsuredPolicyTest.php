<?php

namespace Tests\Unit\Policies;

use App\Models\Insured;
use App\Models\Tenant;
use App\Models\User;
use App\Policies\InsuredPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class InsuredPolicyTest extends TestCase
{
    use RefreshDatabase;

    private InsuredPolicy $policy;
    private Tenant $tenant1;
    private Tenant $tenant2;

    protected function setUp(): void
    {
        parent::setUp();
        $this->policy = new InsuredPolicy();

        Permission::firstOrCreate(['name' => 'view insureds', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'create insureds', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'update insureds', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'delete insureds', 'guard_name' => 'web']);

        $this->tenant1 = Tenant::create([
            'name' => 'Corretora Insured 1',
            'slug' => 'corretora-ins-1',
            'email' => 'ci1@test.com',
            'document' => '11111111000133',
        ]);

        $this->tenant2 = Tenant::create([
            'name' => 'Corretora Insured 2',
            'slug' => 'corretora-ins-2',
            'email' => 'ci2@test.com',
            'document' => '22222222000144',
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

    public function test_view_any(): void
    {
        $userAllowed = $this->createUser($this->tenant1, ['view insureds']);
        $this->assertTrue($this->policy->viewAny($userAllowed));

        $userDenied = $this->createUser($this->tenant1);
        $this->assertFalse($this->policy->viewAny($userDenied));
    }

    public function test_view_requires_same_tenant(): void
    {
        $insured = new Insured();
        $insured->tenant_id = $this->tenant1->id;

        $user1 = $this->createUser($this->tenant1, ['view insureds']);
        $this->assertTrue($this->policy->view($user1, $insured));

        $user2 = $this->createUser($this->tenant2, ['view insureds']);
        $this->assertFalse($this->policy->view($user2, $insured));

        $userNoPerm = $this->createUser($this->tenant1);
        $this->assertFalse($this->policy->view($userNoPerm, $insured));
    }

    public function test_create(): void
    {
        $userAllowed = $this->createUser($this->tenant1, ['create insureds']);
        $this->assertTrue($this->policy->create($userAllowed));

        $userDenied = $this->createUser($this->tenant1);
        $this->assertFalse($this->policy->create($userDenied));
    }

    public function test_update_requires_same_tenant(): void
    {
        $insured = new Insured();
        $insured->tenant_id = $this->tenant1->id;

        $userOk = $this->createUser($this->tenant1, ['update insureds']);
        $this->assertTrue($this->policy->update($userOk, $insured));

        $userDifferent = $this->createUser($this->tenant2, ['update insureds']);
        $this->assertFalse($this->policy->update($userDifferent, $insured));

        $userNoPerm = $this->createUser($this->tenant1);
        $this->assertFalse($this->policy->update($userNoPerm, $insured));
    }

    public function test_delete_requires_same_tenant(): void
    {
        $insured = new Insured();
        $insured->tenant_id = $this->tenant1->id;

        $userOk = $this->createUser($this->tenant1, ['delete insureds']);
        $this->assertTrue($this->policy->delete($userOk, $insured));

        $userWrong = $this->createUser($this->tenant2, ['delete insureds']);
        $this->assertFalse($this->policy->delete($userWrong, $insured));

        $userNoPerm = $this->createUser($this->tenant1);
        $this->assertFalse($this->policy->delete($userNoPerm, $insured));
    }
}
