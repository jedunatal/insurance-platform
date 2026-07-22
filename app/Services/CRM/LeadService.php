<?php

// Namespace alinhado com a estrutura de pastas app/Services/CRM
namespace App\Services\CRM;

// Actions e DTOs da camada de domínio do Lead
use App\Actions\Lead\CreateLeadAction;
use App\Actions\Lead\DeleteLeadAction;
use App\Actions\Lead\UpdateLeadAction;
use App\DTOs\LeadDTO;
use App\Enums\LeadStatusEnum;
use App\Models\Lead;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\ModelNotFoundException;

final class LeadService
{
    /**
     * Injeção de dependência das Actions responsáveis pelas operações de escrita
     */
    public function __construct(
        private readonly CreateLeadAction $createAction,
        private readonly UpdateLeadAction $updateAction,
        private readonly DeleteLeadAction $deleteAction,
    ) {
    }

    /**
     * Executa a criação de um novo Lead via Action
     */
    public function create(LeadDTO $dto): Lead
    {
        return $this->createAction->execute($dto);
    }

    /**
     * Executa a atualização de um Lead existente via Action
     */
    public function update(Lead $lead, LeadDTO $dto): Lead
    {
        return $this->updateAction->execute($lead, $dto);
    }

    /**
     * Executa a remoção do Lead via Action
     */
    public function delete(Lead $lead): void
    {
        $this->deleteAction->execute($lead);
    }

    /**
     * Método wrapper para integração simplificada com o Livewire (ListAll)
     */
    public function paginate(
        ?string $search = null,
        ?LeadStatusEnum $status = null,
        ?int $tenantId = null,
        int $perPage = 15
    ): LengthAwarePaginator {
        // Resolve o tenant do usuário logado caso não seja informado explicitamente
        $tenantId ??= auth()->user()?->tenant_id ?? 1;

        return $this->list(
            tenantId: $tenantId,
            search: $search,
            status: $status,
            perPage: $perPage
        );
    }

    /**
     * Retorna a listagem de Leads filtrada e paginada para um Tenant
     */
    public function list(
        int $tenantId,
        ?string $search = null,
        ?LeadStatusEnum $status = null,
        ?int $assignedTo = null,
        int $perPage = 15,
    ): LengthAwarePaginator {
        return Lead::query()
            ->forTenant($tenantId)
            ->when(
                $search,
                fn ($query) => $query->search($search)
            )
            ->when(
                $status,
                fn ($query) => $query->byStatus($status)
            )
            ->when(
                $assignedTo,
                fn ($query) => $query->byConsultant($assignedTo)
            )
            ->with([
                'tenant:id,name',
                'product:id,name', // Eager loading do Produto/Ramo de seguro
                'assignedTo:id,name',
                'createdBy:id,name',
            ])
            ->latest()
            ->paginate($perPage);
    }

    /**
     * Busca um Lead específico restrito ao Tenant atual
     *
     * @throws ModelNotFoundException
     */
    public function findForTenant(
        int $id,
        int $tenantId
    ): Lead {
        return Lead::query()
            ->forTenant($tenantId)
            ->with([
                'tenant:id,name',
                'product:id,name', // Eager loading do Produto/Ramo
                'assignedTo:id,name',
                'createdBy:id,name',
            ])
            ->findOrFail($id);
    }

    /**
     * Totaliza os leads ativos (em negociação)
     */
    public function countActive(int $tenantId): int
    {
        return Lead::query()
            ->forTenant($tenantId)
            ->active()
            ->count();
    }

    /**
     * Totaliza os leads criados na data de hoje
     */
    public function countToday(int $tenantId): int
    {
        return Lead::query()
            ->forTenant($tenantId)
            ->createdToday()
            ->count();
    }

    /**
     * Totaliza os leads com agendamento de contato atrasado
     */
    public function countOverdue(int $tenantId): int
    {
        return Lead::query()
            ->forTenant($tenantId)
            ->overdue()
            ->count();
    }

    /**
     * Agrupa e retorna a quantidade de leads por cada status do Enum
     *
     * @return array<string, int>
     */
    public function countByStatus(int $tenantId): array
    {
        $counts = Lead::query()
            ->forTenant($tenantId)
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status')
            ->all();

        return array_merge(
            array_fill_keys(
                array_map(
                    fn (LeadStatusEnum $status) => $status->value,
                    LeadStatusEnum::cases()
                ),
                0
            ),
            $counts
        );
    }
}