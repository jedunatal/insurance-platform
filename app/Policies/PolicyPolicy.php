<?php

namespace App\Policies;

use App\Models\Policy;
use App\Models\User;

/**
 * Policy de autorização do módulo de Apólices.
 *
 * Concede acesso com base em permissões do Spatie Permissions,
 * via $user->checkPermissionTo(), e garante isolamento multi-tenant.
 */
final class PolicyPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->checkPermissionTo('view policies');
    }

    public function view(User $user, Policy $policy): bool
    {
        return $user->checkPermissionTo('view policies')
            && $this->belongsToSameTenant($user, $policy);
    }

    public function create(User $user): bool
    {
        return $user->checkPermissionTo('create policies');
    }

    public function update(User $user, Policy $policy): bool
    {
        return $user->checkPermissionTo('update policies')
            && $this->belongsToSameTenant($user, $policy);
    }

    public function delete(User $user, Policy $policy): bool
    {
        return $user->checkPermissionTo('delete policies')
            && $this->belongsToSameTenant($user, $policy);
    }

    public function restore(User $user, Policy $policy): bool
    {
        return $user->checkPermissionTo('delete policies')
            && $this->belongsToSameTenant($user, $policy);
    }

    public function forceDelete(User $user, Policy $policy): bool
    {
        return $user->checkPermissionTo('delete policies')
            && $this->belongsToSameTenant($user, $policy);
    }

    private function belongsToSameTenant(User $user, Policy $policy): bool
    {
        return $user->tenant_id === $policy->tenant_id;
    }
}
