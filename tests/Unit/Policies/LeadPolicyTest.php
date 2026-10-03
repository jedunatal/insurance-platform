<?php

namespace Tests\Unit\Policies;

use App\Models\Lead;
use App\Models\User;
use App\Policies\LeadPolicy;
use Tests\TestCase;

class LeadPolicyTest extends TestCase
{
    private LeadPolicy $policy;

    protected function setUp(): void
    {
        parent::setUp();
        $this->policy = new LeadPolicy();
    }

    private function createUserWithPermission(int $tenantId, string $permission, bool $hasPermission = true): User
    {
        /** @var User&\PHPUnit\Framework\MockObject\MockObject $user */
        $user = $this->getMockBuilder(User::class)
            ->onlyMethods(['checkPermissionTo'])
            ->getMock();

        $user->tenant_id = $tenantId;
        $user->method('checkPermissionTo')
            ->with($permission)
            ->willReturn($hasPermission);

        return $user;
    }

    public function test_view_any(): void
    {
        $userAllowed = $this->createUserWithPermission(1, 'view leads', true);
        $this->assertTrue($this->policy->viewAny($userAllowed));

        $userDenied = $this->createUserWithPermission(1, 'view leads', false);
        $this->assertFalse($this->policy->viewAny($userDenied));
    }

    public function test_view_requires_same_tenant(): void
    {
        $lead = new Lead();
        $lead->tenant_id = 1;

        $user1 = $this->createUserWithPermission(1, 'view leads', true);
        $this->assertTrue($this->policy->view($user1, $lead));

        $user2 = $this->createUserWithPermission(2, 'view leads', true);
        $this->assertFalse($this->policy->view($user2, $lead));
    }

    public function test_create(): void
    {
        $user = $this->createUserWithPermission(1, 'create leads', true);
        $this->assertTrue($this->policy->create($user));

        $userNoPerm = $this->createUserWithPermission(1, 'create leads', false);
        $this->assertFalse($this->policy->create($userNoPerm));
    }

    public function test_update_requires_same_tenant(): void
    {
        $lead = new Lead();
        $lead->tenant_id = 10;

        $userOk = $this->createUserWithPermission(10, 'update leads', true);
        $this->assertTrue($this->policy->update($userOk, $lead));

        $userDifferentTenant = $this->createUserWithPermission(20, 'update leads', true);
        $this->assertFalse($this->policy->update($userDifferentTenant, $lead));
    }

    public function test_delete_requires_same_tenant(): void
    {
        $lead = new Lead();
        $lead->tenant_id = 15;

        $userOk = $this->createUserWithPermission(15, 'delete leads', true);
        $this->assertTrue($this->policy->delete($userOk, $lead));

        $userWrongTenant = $this->createUserWithPermission(30, 'delete leads', true);
        $this->assertFalse($this->policy->delete($userWrongTenant, $lead));
    }
}
