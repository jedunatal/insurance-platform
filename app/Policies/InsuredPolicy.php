<?php

namespace App\Policies;

use App\Models\Insured;
use App\Models\User;

final class InsuredPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->checkPermissionTo('view insureds');
    }

    public function view(User $user, Insured $insured): bool
    {
        return $user->checkPermissionTo('view insureds')
            && $this->belongsToSameTenant($user, $insured);
    }

    public function create(User $user): bool
    {
        return $user->checkPermissionTo('create insureds');
    }

    public function update(User $user, Insured $insured): bool
    {
        return $user->checkPermissionTo('update insureds')
            && $this->belongsToSameTenant($user, $insured);
    }

    public function delete(User $user, Insured $insured): bool
    {
        return $user->checkPermissionTo('delete insureds')
            && $this->belongsToSameTenant($user, $insured);
    }

    public function restore(User $user, Insured $insured): bool
    {
        return $user->checkPermissionTo('delete insureds')
            && $this->belongsToSameTenant($user, $insured);
    }

    public function forceDelete(User $user, Insured $insured): bool
    {
        return $user->checkPermissionTo('delete insureds')
            && $this->belongsToSameTenant($user, $insured);
    }

    private function belongsToSameTenant(User $user, Insured $insured): bool
    {
        return $user->tenant_id === $insured->tenant_id;
    }
}