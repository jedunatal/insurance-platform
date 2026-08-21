<?php

namespace App\Policies;

use App\Models\PolicyInstallment;
use App\Models\User;

final class FinancialPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->checkPermissionTo('view financial') || $user->hasRole(['super-admin', 'admin', 'broker', 'consultant', 'assistant']);
    }

    public function view(User $user, PolicyInstallment $installment): bool
    {
        return ($user->checkPermissionTo('view financial') || $user->hasRole(['super-admin', 'admin', 'broker', 'consultant', 'assistant']))
            && $this->belongsToSameTenant($user, $installment);
    }

    public function update(User $user, PolicyInstallment $installment): bool
    {
        return ($user->checkPermissionTo('update financial') || $user->hasRole(['super-admin', 'admin', 'broker']))
            && $this->belongsToSameTenant($user, $installment);
    }

    public function liquidate(User $user, PolicyInstallment $installment): bool
    {
        return ($user->checkPermissionTo('liquidate financial') || $user->hasRole(['super-admin', 'admin', 'broker']))
            && $this->belongsToSameTenant($user, $installment);
    }

    public function delete(User $user, PolicyInstallment $installment): bool
    {
        return ($user->checkPermissionTo('delete financial') || $user->hasRole(['super-admin', 'admin', 'broker']))
            && $this->belongsToSameTenant($user, $installment);
    }

    private function belongsToSameTenant(User $user, PolicyInstallment $installment): bool
    {
        return $user->tenant_id === $installment->tenant_id;
    }
}
