<?php

namespace App\Policies;

use App\Models\Lead;
use App\Models\User;

final class LeadPolicy
{
    /**
     * Pode visualizar a listagem.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Pode visualizar um Lead.
     */
    public function view(User $user, Lead $lead): bool
    {
        return true;
    }

    /**
     * Pode criar.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Pode atualizar.
     */
    public function update(User $user, Lead $lead): bool
    {
        return true;
    }

    /**
     * Soft Delete.
     */
    public function delete(User $user, Lead $lead): bool
    {
        return true;
    }

    /**
     * Restaurar.
     */
    public function restore(User $user, Lead $lead): bool
    {
        return true;
    }

    /**
     * Exclusão definitiva.
     */
    public function forceDelete(User $user, Lead $lead): bool
    {
        return false;
    }
}