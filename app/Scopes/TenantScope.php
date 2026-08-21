<?php

namespace App\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class TenantScope implements Scope
{
    /**
     * Aplica o filtro de tenant automaticamente em todas as consultas Eloquent.
     */
    public function apply(Builder $builder, Model $model): void
    {
        if (auth()->check()) {
            $tenantId = auth()->user()?->tenant_id;
            if ($tenantId) {
                $builder->where($model->qualifyColumn('tenant_id'), $tenantId);
            }
            return;
        }

        // Em ambientes de teste ou dev local sem usuário autenticado, usa tenant padrão se configurado
        if (app()->environment('local', 'testing')) {
            $tenantId = auth()->user()?->tenant_id ?? 1;
            if ($tenantId) {
                $builder->where($model->qualifyColumn('tenant_id'), $tenantId);
            }
        }
    }
}
