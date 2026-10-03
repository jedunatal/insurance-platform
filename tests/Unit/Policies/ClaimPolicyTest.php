<?php

namespace Tests\Unit\Policies;

use App\Models\Claim;
use App\Models\User;
use App\Policies\ClaimPolicy;
use Tests\TestCase;

class ClaimPolicyTest extends TestCase
{
    private ClaimPolicy $policy;

    protected function setUp(): void
    {
        parent::setUp();
        $this->policy = new ClaimPolicy();
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
        $userAllowed = $this->createUserWithPermission(1, 'view claims', true);
        $this->assertTrue($this->policy->viewAny($userAllowed));

        $userDenied = $this->createUserWithPermission(1, 'view claims', false);
        $this->assertFalse($this->policy->viewAny($userDenied));
    }

    public function test_view_requires_same_tenant(): void
    {
        $claim = new Claim();
        $claim->tenant_id = 1;

        $user1 = $this->createUserWithPermission(1, 'view claims', true);
        $this->assertTrue($this->policy->view($user1, $claim));

        $user2 = $this->createUserWithPermission(2, 'view claims', true);
        $this->assertFalse($this->policy->view($user2, $claim));
    }

    public function test_create(): void
    {
        $user = $this->createUserWithPermission(1, 'create claims', true);
        $this->assertTrue($this->policy->create($user));

        $userNoPerm = $this->createUserWithPermission(1, 'create claims', false);
        $this->assertFalse($this->policy->create($userNoPerm));
    }

    public function test_update_requires_same_tenant(): void
    {
        $claim = new Claim();
        $claim->tenant_id = 7;

        $userOk = $this->createUserWithPermission(7, 'update claims', true);
        $this->assertTrue($this->policy->update($userOk, $claim));

        $userDifferent = $this->createUserWithPermission(8, 'update claims', true);
        $this->assertFalse($this->policy->update($userDifferent, $claim));
    }

    public function test_delete_requires_same_tenant(): void
    {
        $claim = new Claim();
        $claim->tenant_id = 14;

        $userOk = $this->createUserWithPermission(14, 'delete claims', true);
        $this->assertTrue($this->policy->delete($userOk, $claim));

        $userWrongTenant = $this->createUserWithPermission(99, 'delete claims', true);
        $this->assertFalse($this->policy->delete($userWrongTenant, $claim));
    }
}
