<?php

namespace Tests\Unit\Policies;

use App\Models\Policy;
use App\Models\User;
use App\Policies\PolicyPolicy;
use Tests\TestCase;

class PolicyPolicyTest extends TestCase
{
    private PolicyPolicy $policy;

    protected function setUp(): void
    {
        parent::setUp();
        $this->policy = new PolicyPolicy();
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

    public function test_view_any_checks_permission(): void
    {
        $userAllowed = $this->createUserWithPermission(1, 'view policies', true);
        $this->assertTrue($this->policy->viewAny($userAllowed));

        $userDenied = $this->createUserWithPermission(1, 'view policies', false);
        $this->assertFalse($this->policy->viewAny($userDenied));
    }

    public function test_view_requires_both_permission_and_same_tenant(): void
    {
        $policyModel = new Policy();
        $policyModel->tenant_id = 1;

        // Caso 1: Tem permissão e mesmo tenant
        $user1 = $this->createUserWithPermission(1, 'view policies', true);
        $this->assertTrue($this->policy->view($user1, $policyModel));

        // Caso 2: Tem permissão mas tenant diferente
        $user2 = $this->createUserWithPermission(2, 'view policies', true);
        $this->assertFalse($this->policy->view($user2, $policyModel));

        // Caso 3: Mesmo tenant mas sem permissão
        $user3 = $this->createUserWithPermission(1, 'view policies', false);
        $this->assertFalse($this->policy->view($user3, $policyModel));
    }

    public function test_create_checks_permission(): void
    {
        $user = $this->createUserWithPermission(1, 'create policies', true);
        $this->assertTrue($this->policy->create($user));

        $userNoPerm = $this->createUserWithPermission(1, 'create policies', false);
        $this->assertFalse($this->policy->create($userNoPerm));
    }

    public function test_update_requires_permission_and_same_tenant(): void
    {
        $policyModel = new Policy();
        $policyModel->tenant_id = 10;

        $userAuthorized = $this->createUserWithPermission(10, 'update policies', true);
        $this->assertTrue($this->policy->update($userAuthorized, $policyModel));

        $userDifferentTenant = $this->createUserWithPermission(20, 'update policies', true);
        $this->assertFalse($this->policy->update($userDifferentTenant, $policyModel));

        $userNoPerm = $this->createUserWithPermission(10, 'update policies', false);
        $this->assertFalse($this->policy->update($userNoPerm, $policyModel));
    }

    public function test_delete_requires_permission_and_same_tenant(): void
    {
        $policyModel = new Policy();
        $policyModel->tenant_id = 5;

        $user = $this->createUserWithPermission(5, 'delete policies', true);
        $this->assertTrue($this->policy->delete($user, $policyModel));

        $userDenied = $this->createUserWithPermission(5, 'delete policies', false);
        $this->assertFalse($this->policy->delete($userDenied, $policyModel));

        $userWrongTenant = $this->createUserWithPermission(99, 'delete policies', true);
        $this->assertFalse($this->policy->delete($userWrongTenant, $policyModel));
    }
}
