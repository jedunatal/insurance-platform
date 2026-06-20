<?php

namespace App\Services\CRM;

use App\Actions\CRM\CreateLeadAction;
use App\DTOs\LeadDTO;
use App\Enums\LeadStatusEnum;
use App\Models\Lead;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\ModelNotFoundException;

final class LeadService
{
    public function __construct(
        private readonly CreateLeadAction $createLeadAction,
    ) {}

    // ─── Escrita ──────────────────────────────────────────────────────────────

    public function create(LeadDTO $dto): Lead
    {
        return $this->createLeadAction->execute($dto);
    }

    // ─── Leitura ──────────────────────────────────────────────────────────────

    /**
     * Listagem paginada com filtros opcionais.
     * Escopada por tenant (multi-tenancy) e opcionalmente por consultor.
     */
    public function list(
        int              $tenantId,
        ?string          $search      = null,
        ?LeadStatusEnum  $status      = null,
        ?int             $assignedTo  = null,
        int              $perPage     = 15,
    ): LengthAwarePaginator {
        return Lead::query()
            ->forTenant($tenantId)
            ->when($search,     fn ($q) => $q->search($search))
            ->when($status,     fn ($q) => $q->byStatus($status))
            ->when($assignedTo, fn ($q) => $q->byConsultant($assignedTo))
            ->with(['assignedTo:id,name', 'createdBy:id,name'])
            ->latest()
            ->paginate($perPage);
    }

    /**
     * Busca um Lead pelo ID validando o tenant (evita IDOR).
     *
     * @throws ModelNotFoundException
     */
    public function findForTenant(int $id, int $tenantId): Lead
    {
        return Lead::query()
            ->forTenant($tenantId)
            ->with(['assignedTo:id,name', 'createdBy:id,name'])
            ->findOrFail($id);
    }

    // ─── Métricas para Dashboard ──────────────────────────────────────────────

    /** Total de leads ativos (não convertidos nem perdidos) do tenant. */
    public function countActive(int $tenantId): int
    {
        return Lead::query()
            ->forTenant($tenantId)
            ->active()
            ->count();
    }

    /** Leads criados hoje. */
    public function countToday(int $tenantId): int
    {
        return Lead::query()
            ->forTenant($tenantId)
            ->whereDate('created_at', today())
            ->count();
    }

    /** Leads com próximo contato vencido. */
    public function countOverdue(int $tenantId): int
    {
        return Lead::query()
            ->forTenant($tenantId)
            ->overdue()
            ->count();
    }
}