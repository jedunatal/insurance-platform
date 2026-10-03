<?php

namespace Tests\Unit\Policies;

use App\Models\Lead;
use App\Models\Tenant;
use App\Models\User;
use App\Policies\LeadPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class LeadPolicyTest extends TestCase
{
    use RefreshDatabase;

    private LeadPolicy $policy;
    private Tenant $tenant1;
    private Tenant $tenant2;

    protected function setUp(): void
    {
        parent::setUp();
        $this->policy = new LeadPolicy();

        Permission::firstOrCreate(['name' => 'view leads', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'create leads', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'update leads', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'delete leads', 'guard_name' => 'web']);

        $this->tenant1 = Tenant::create([
            'name' => 'Corretora 1',
            'slug' => 'corretora-lead-1',
            'email' => 'cl1@test.com',
            'document' => '11111111000111',
        ]);

        $this->tenant2 = Tenant::create([
            'name' => 'Corretora 2',
            'slug' => 'corretora-lead-2',
            'email' => 'cl2@test.com',
            'document' => '22222222000122',
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
        $userAllowed = $this->createUser($this->tenant1, ['view leads']);
        $this->assertTrue($this->policy->viewAny($userAllowed));

        $userDenied = $this->createUser($this->tenant1);
        $this->assertFalse($this->policy->viewAny($userDenied));
    }

    public function test_view_requires_same_tenant(): void
    {
        $lead = new Lead();
        $lead->tenant_id = $this->tenant1->id;

        $user1 = $this->createUser($this->tenant1, ['view leads']);
        $this->assertTrue($this->policy->view($user1, $lead));

        $user2 = $this->createUser($this->tenant2, ['view leads']);
        $this->assertFalse($this->policy->view($user2, $lead));

        $userNoPerm = $this->createUser($this->tenant1);
        $this->assertFalse($this->policy->view($userNoPerm, $lead));
    }

    public function test_create(): void
    {
        $userAllowed = $this->createUser($this->tenant1, ['create leads']);
        $this->assertTrue($this->policy->create($userAllowed));

        $userDenied = $this->createUser($this->tenant1);
        $this->assertFalse($this->policy->create($userDenied));
    }

    public function test_update_requires_same_tenant(): void
    {
        $lead = new Lead();
        $lead->tenant_id = $this->tenant1->id;

        $userOk = $this->createUser($this->tenant1, ['update leads']);
        $this->assertTrue($this->policy->update($userOk, $lead));

        $userDifferent = $this->createUser($this->tenant2, ['update leads']);
        $this->assertFalse($this->policy->update($userDifferent, $lead));

        $userNoPerm = $this->createUser($this->tenant1);
        $this->assertFalse($this->policy->update($userNoPerm, $lead));
    }

    public function test_delete_requires_same_tenant(): void
    {
        $lead = new Lead();
        $lead->tenant_id = $this->tenant1->id;

        $userOk = $this->createUser($this->tenant1, ['delete leads']);
        $this->assertTrue($this->policy->delete($userOk, $lead));

        $userWrong = $this->createUser($this->tenant2, ['delete leads']);
        $this->assertFalse($this->policy->delete($userWrong, $lead));

        $userNoPerm = $this->createUser($this->tenant1);
        $this->assertFalse($this->policy->delete($userNoPerm, $lead));
    }
}
