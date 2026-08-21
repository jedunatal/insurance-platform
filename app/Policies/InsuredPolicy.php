<?php

namespace App\Policies;

use App\Models\Insured;
use App\Models\User;

final class InsuredPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasRole(['super-admin', 'admin', 'broker', 'consultant', 'assistant'])
            || $user->checkPermissionTo('view insureds');
    }

    public function view(User $user, Insured $insured): bool
    {
        return ($user->hasRole(['super-admin', 'admin', 'broker', 'consultant', 'assistant']) || $user->checkPermissionTo('view insureds'))
            && $this->belongsToSameTenant($user, $insured);
    }

    public function create(User $user): bool
    {
        return $user->hasRole(['super-admin', 'admin', 'broker', 'consultant', 'assistant'])
            || $user->checkPermissionTo('create insureds');
    }

    public function update(User $user, Insured $insured): bool
    {
        return ($user->hasRole(['super-admin', 'admin', 'broker', 'consultant', 'assistant']) || $user->checkPermissionTo('update insureds'))
            && $this->belongsToSameTenant($user, $insured);
    }

    public function delete(User $user, Insured $insured): bool
    {
        // Apenas admin e broker podem excluir segurados
        return ($user->hasRole(['super-admin', 'admin', 'broker']) || $user->checkPermissionTo('delete insureds'))
            && ! $user->hasRole(['assistant', 'consultant'])
            && $this->belongsToSameTenant($user, $insured);
    }

    public function restore(User $user, Insured $insured): bool
    {
        return ($user->hasRole(['super-admin', 'admin', 'broker']) || $user->checkPermissionTo('delete insureds'))
            && $this->belongsToSameTenant($user, $insured);
    }

    public function forceDelete(User $user, Insured $insured): bool
    {
        return ($user->hasRole(['super-admin', 'admin', 'broker']) || $user->checkPermissionTo('delete insureds'))
            && ! $user->hasRole(['assistant', 'consultant'])
            && $this->belongsToSameTenant($user, $insured);
    }

    private function belongsToSameTenant(User $user, Insured $insured): bool
    {
        return $user->tenant_id === $insured->tenant_id;
    }
}