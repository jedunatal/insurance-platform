<?php

namespace App\Policies;

use App\Models\Claim;
use App\Models\User;

final class ClaimPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->checkPermissionTo('view claims');
    }

    public function view(User $user, Claim $claim): bool
    {
        return $user->checkPermissionTo('view claims')
            && $this->belongsToSameTenant($user, $claim);
    }

    public function create(User $user): bool
    {
        return $user->checkPermissionTo('create claims');
    }

    public function update(User $user, Claim $claim): bool
    {
        return $user->checkPermissionTo('update claims')
            && $this->belongsToSameTenant($user, $claim);
    }

    public function delete(User $user, Claim $claim): bool
    {
        return $user->checkPermissionTo('delete claims')
            && $this->belongsToSameTenant($user, $claim);
    }

    public function restore(User $user, Claim $claim): bool
    {
        return $user->checkPermissionTo('delete claims')
            && $this->belongsToSameTenant($user, $claim);
    }

    public function forceDelete(User $user, Claim $claim): bool
    {
        return $user->checkPermissionTo('delete claims')
            && $this->belongsToSameTenant($user, $claim);
    }

    private function belongsToSameTenant(User $user, Claim $claim): bool
    {
        return $user->tenant_id === $claim->tenant_id;
    }
}