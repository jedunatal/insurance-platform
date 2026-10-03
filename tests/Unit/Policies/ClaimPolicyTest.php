<?php

namespace Tests\Unit\Policies;

use App\Models\Claim;
use App\Models\Tenant;
use App\Models\User;
use App\Policies\ClaimPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class ClaimPolicyTest extends TestCase
{
    use RefreshDatabase;

    private ClaimPolicy $policy;
    private Tenant $tenant1;
    private Tenant $tenant2;

    protected function setUp(): void
    {
        parent::setUp();
        $this->policy = new ClaimPolicy();

        Permission::firstOrCreate(['name' => 'view claims', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'create claims', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'update claims', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'delete claims', 'guard_name' => 'web']);

        $this->tenant1 = Tenant::create([
            'name' => 'Corretora Claim 1',
            'slug' => 'corretora-claim-1',
            'email' => 'cc1@test.com',
            'document' => '11111111000155',
        ]);

        $this->tenant2 = Tenant::create([
            'name' => 'Corretora Claim 2',
            'slug' => 'corretora-claim-2',
            'email' => 'cc2@test.com',
            'document' => '22222222000166',
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
        $userAllowed = $this->createUser($this->tenant1, ['view claims']);
        $this->assertTrue($this->policy->viewAny($userAllowed));

        $userDenied = $this->createUser($this->tenant1);
        $this->assertFalse($this->policy->viewAny($userDenied));
    }

    public function test_view_requires_same_tenant(): void
    {
        $claim = new Claim();
        $claim->tenant_id = $this->tenant1->id;

        $user1 = $this->createUser($this->tenant1, ['view claims']);
        $this->assertTrue($this->policy->view($user1, $claim));

        $user2 = $this->createUser($this->tenant2, ['view claims']);
        $this->assertFalse($this->policy->view($user2, $claim));

        $userNoPerm = $this->createUser($this->tenant1);
        $this->assertFalse($this->policy->view($userNoPerm, $claim));
    }

    public function test_create(): void
    {
        $userAllowed = $this->createUser($this->tenant1, ['create claims']);
        $this->assertTrue($this->policy->create($userAllowed));

        $userDenied = $this->createUser($this->tenant1);
        $this->assertFalse($this->policy->create($userDenied));
    }

    public function test_update_requires_same_tenant(): void
    {
        $claim = new Claim();
        $claim->tenant_id = $this->tenant1->id;

        $userOk = $this->createUser($this->tenant1, ['update claims']);
        $this->assertTrue($this->policy->update($userOk, $claim));

        $userDifferent = $this->createUser($this->tenant2, ['update claims']);
        $this->assertFalse($this->policy->update($userDifferent, $claim));

        $userNoPerm = $this->createUser($this->tenant1);
        $this->assertFalse($this->policy->update($userNoPerm, $claim));
    }

    public function test_delete_requires_same_tenant(): void
    {
        $claim = new Claim();
        $claim->tenant_id = $this->tenant1->id;

        $userOk = $this->createUser($this->tenant1, ['delete claims']);
        $this->assertTrue($this->policy->delete($userOk, $claim));

        $userWrong = $this->createUser($this->tenant2, ['delete claims']);
        $this->assertFalse($this->policy->delete($userWrong, $claim));

        $userNoPerm = $this->createUser($this->tenant1);
        $this->assertFalse($this->policy->delete($userNoPerm, $claim));
    }
}
