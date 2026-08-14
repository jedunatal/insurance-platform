<?php

namespace App\Policies;

use App\Models\Claim;
use App\Models\User;

class ClaimPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->checkPermissionTo('view claims');
    }

    public function view(User $user, Claim $claim): bool
    {
        return $user->checkPermissionTo('view claims') && $user->tenant_id === $claim->tenant_id;
    }

    public function create(User $user): bool
    {
        return $user->checkPermissionTo('create claims');
    }

    public function update(User $user, Claim $claim): bool
    {
        return $user->checkPermissionTo('update claims') && $user->tenant_id === $claim->tenant_id;
    }

    public function delete(User $user, Claim $claim): bool
    {
        return $user->checkPermissionTo('delete claims') && $user->tenant_id === $claim->tenant_id;
    }
}