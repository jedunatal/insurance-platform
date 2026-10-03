<?php

namespace Tests\Unit\Policies;

use App\Models\Insured;
use App\Models\User;
use App\Policies\InsuredPolicy;
use Tests\TestCase;

class InsuredPolicyTest extends TestCase
{
    private InsuredPolicy $policy;

    protected function setUp(): void
    {
        parent::setUp();
        $this->policy = new InsuredPolicy();
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
        $userAllowed = $this->createUserWithPermission(1, 'view insureds', true);
        $this->assertTrue($this->policy->viewAny($userAllowed));

        $userDenied = $this->createUserWithPermission(1, 'view insureds', false);
        $this->assertFalse($this->policy->viewAny($userDenied));
    }

    public function test_view_requires_same_tenant(): void
    {
        $insured = new Insured();
        $insured->tenant_id = 1;

        $user1 = $this->createUserWithPermission(1, 'view insureds', true);
        $this->assertTrue($this->policy->view($user1, $insured));

        $user2 = $this->createUserWithPermission(2, 'view insureds', true);
        $this->assertFalse($this->policy->view($user2, $insured));
    }

    public function test_create(): void
    {
        $user = $this->createUserWithPermission(1, 'create insureds', true);
        $this->assertTrue($this->policy->create($user));

        $userNoPerm = $this->createUserWithPermission(1, 'create insureds', false);
        $this->assertFalse($this->policy->create($userNoPerm));
    }

    public function test_update_requires_same_tenant(): void
    {
        $insured = new Insured();
        $insured->tenant_id = 8;

        $userOk = $this->createUserWithPermission(8, 'update insureds', true);
        $this->assertTrue($this->policy->update($userOk, $insured));

        $userDifferent = $this->createUserWithPermission(9, 'update insureds', true);
        $this->assertFalse($this->policy->update($userDifferent, $insured));
    }

    public function test_delete_requires_same_tenant(): void
    {
        $insured = new Insured();
        $insured->tenant_id = 12;

        $userOk = $this->createUserWithPermission(12, 'delete insureds', true);
        $this->assertTrue($this->policy->delete($userOk, $insured));

        $userWrongTenant = $this->createUserWithPermission(15, 'delete insureds', true);
        $this->assertFalse($this->policy->delete($userWrongTenant, $insured));
    }
}
