<?php

namespace App\Policies;

use App\Models\Lead;
use App\Models\User;

final class LeadPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasRole(['super-admin', 'admin', 'broker', 'consultant', 'assistant'])
            || $user->checkPermissionTo('view leads');
    }

    public function view(User $user, Lead $lead): bool
    {
        return ($user->hasRole(['super-admin', 'admin', 'broker', 'consultant', 'assistant']) || $user->checkPermissionTo('view leads'))
            && $this->belongsToSameTenant($user, $lead);
    }

    public function create(User $user): bool
    {
        return $user->hasRole(['super-admin', 'admin', 'broker', 'consultant', 'assistant'])
            || $user->checkPermissionTo('create leads');
    }

    public function update(User $user, Lead $lead): bool
    {
        return ($user->hasRole(['super-admin', 'admin', 'broker', 'consultant', 'assistant']) || $user->checkPermissionTo('update leads'))
            && $this->belongsToSameTenant($user, $lead);
    }

    public function delete(User $user, Lead $lead): bool
    {
        return ($user->hasRole(['super-admin', 'admin', 'broker']) || $user->checkPermissionTo('delete leads'))
            && ! $user->hasRole(['assistant', 'consultant'])
            && $this->belongsToSameTenant($user, $lead);
    }

    public function restore(User $user, Lead $lead): bool
    {
        return ($user->hasRole(['super-admin', 'admin', 'broker']) || $user->checkPermissionTo('delete leads'))
            && $this->belongsToSameTenant($user, $lead);
    }

    public function forceDelete(User $user, Lead $lead): bool
    {
        return ($user->hasRole(['super-admin', 'admin', 'broker']) || $user->checkPermissionTo('delete leads'))
            && ! $user->hasRole(['assistant', 'consultant'])
            && $this->belongsToSameTenant($user, $lead);
    }

    private function belongsToSameTenant(User $user, Lead $lead): bool
    {
        return $user->tenant_id === $lead->tenant_id;
    }
}