<?php

namespace App\Services\Insurance;

use App\Actions\Policy\CreatePolicyAction;
use App\Actions\Policy\DeletePolicyAction;
use App\Actions\Policy\UpdatePolicyAction;
use App\DTOs\PolicyData;
use App\Enums\PolicyPaymentMethodEnum;
use App\Enums\PolicyStatusEnum;
use App\Models\Insured;
use App\Models\Policy;
use App\Models\Product;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Collection;
use RuntimeException;

/**
 * Service responsável pelo domínio de Apólices.
 *
 * Orquestra Actions e centraliza regras financeiras
 * (cálculo de IOF, total de prêmio e geração de parcelas).
 */
final class PolicyService
{
    /**
     * Alíquota padrão de IOF sobre seguros (Porto Seguro / SUSEP).
     */
    public const IOF_RATE = 0.0738;

    public function __construct(
        private readonly CreatePolicyAction $createAction,
        private readonly UpdatePolicyAction $updateAction,
        private readonly DeletePolicyAction $deleteAction,
    ) {
    }

    public function create(PolicyData $dto): Policy
    {
        return $this->createAction->execute($dto);
    }

    public function update(Policy $policy, PolicyData $dto): Policy
    {
        return $this->updateAction->execute($policy, $dto);
    }

    public function delete(Policy $policy): void
    {
        $this->deleteAction->execute($policy);
    }

    /*
    |--------------------------------------------------------------------------
    | Listagem & Consultas
    |--------------------------------------------------------------------------
    */

    public function paginate(
        ?string $search = null,
        ?PolicyStatusEnum $status = null,
        ?int $tenantId = null,
        int $perPage = 15
    ): LengthAwarePaginator {
        $tenantId ??= $this->resolveTenantId();

        return $this->list(
            tenantId: $tenantId,
            search: $search,
            status: $status,
            perPage: $perPage
        );
    }

    public function list(
        int $tenantId,
        ?string $search = null,
        ?PolicyStatusEnum $status = null,
        ?int $brokerId = null,
        ?int $insuredId = null,
        int $perPage = 15,
    ): LengthAwarePaginator {
        return Policy::query()
            ->forTenant($tenantId)
            ->when($search, fn ($q) => $q->search($search))
            ->when($status, fn ($q) => $q->byStatus($status))
            ->when($brokerId, fn ($q) => $q->byBroker($brokerId))
            ->when($insuredId, fn ($q) => $q->byInsured($insuredId))
            ->with([
                'insured:id,name,document',
                'product:id,name',
                'broker:id,name',
                'createdBy:id,name',
            ])
            ->latest()
            ->paginate($perPage);
    }

    public function findForTenant(int $id, int $tenantId, array $with = []): Policy
    {
        $with = array_merge($with, [
            'tenant:id,name',
            'insured:id,name,document,email,phone',
            'product:id,name',
            'broker:id,name',
            'createdBy:id,name',
        ]);

        return Policy::query()
            ->forTenant($tenantId)
            ->with($with)
            ->findOrFail($id);
    }

    /*
    |--------------------------------------------------------------------------
    | Indicadores / Dashboard
    |--------------------------------------------------------------------------
    */

    public function countByStatus(int $tenantId): array
    {
        $counts = Policy::query()
            ->forTenant($tenantId)
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status')
            ->all();

        return array_merge(
            array_fill_keys(
                array_map(fn (PolicyStatusEnum $s) => $s->value, PolicyStatusEnum::cases()),
                0
            ),
            $counts
        );
    }

    public function countTotal(int $tenantId): int
    {
        return Policy::query()->forTenant($tenantId)->count();
    }

    public function countActive(int $tenantId): int
    {
        return Policy::query()
            ->forTenant($tenantId)
            ->active()
            ->count();
    }

    public function countToday(int $tenantId): int
    {
        return Policy::query()
            ->forTenant($tenantId)
            ->createdToday()
            ->count();
    }

    public function countExpiringSoon(int $tenantId, int $days = 30): int
    {
        return Policy::query()
            ->forTenant($tenantId)
            ->expiringSoon($days)
            ->count();
    }

    /**
     * @return Collection<int, Policy>
     */
    public function expiringSoon(int $tenantId, int $days = 30): Collection
    {
        return Policy::query()
            ->forTenant($tenantId)
            ->with(['insured:id,name', 'broker:id,name'])
            ->expiringSoon($days)
            ->orderBy('end_date')
            ->get();
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers de opções de formulário
    |--------------------------------------------------------------------------
    */

    /**
     * @return array<int, string>
     */
    public function insuredOptions(int $tenantId): array
    {
        return Insured::query()
            ->when($tenantId > 0, fn ($q) => $q->forTenant($tenantId))
            ->orderBy('name')
            ->pluck('name', 'id')
            ->all();
    }

    /**
     * @return array<int, string>
     */
    public function productOptions(int $tenantId, ?string $branch = null): array
    {
        return Product::query()
            ->when($tenantId > 0, fn ($q) => $q->where('tenant_id', $tenantId))
            ->when($branch, fn ($q) => $q->where('branch', $branch))
            ->where('is_active', true)
            ->orderBy('name')
            ->pluck('name', 'id')
            ->all();
    }

    /**
     * @return array<int, string>
     */
    public function brokerOptions(int $tenantId): array
    {
        return User::query()
            ->when($tenantId > 0, fn ($q) => $q->where('tenant_id', $tenantId))
            ->where('is_active', true)
            ->orderBy('name')
            ->pluck('name', 'id')
            ->all();
    }

    /*
    |--------------------------------------------------------------------------
    | Cálculos Financeiros
    |--------------------------------------------------------------------------
    */

    /**
     * Calcula o IOF sobre o prêmio líquido.
     */
    public function calculateIof(float $netPremium): float
    {
        return round($netPremium * self::IOF_RATE, 2);
    }

    /**
     * Calcula o total de prêmio (líquido + IOF).
     */
    public function calculateTotalPremium(float $netPremium, ?float $iof = null): float
    {
        $iof ??= $this->calculateIof($netPremium);

        return round($netPremium + $iof, 2);
    }

    /**
     * Recalcula e retorna prêmio, IOF e total prontos para gravação.
     *
     * @return array{net_premium: float, iof_amount: float, total_premium: float}
     */
    public function recalculatePremiums(float $netPremium): array
    {
        $iof  = $this->calculateIof($netPremium);
        $total = $this->calculateTotalPremium($netPremium, $iof);

        return [
            'net_premium'   => $netPremium,
            'iof_amount'    => $iof,
            'total_premium' => $total,
        ];
    }

    /**
     * Gera a tabela de parcelas a partir do total de prêmio.
     *
     * @return array<int, array{installment: int, due_date: string, amount: float, status: string}>
     */
    public function generateInstallmentSchedule(
        float $totalPremium,
        int $installmentsCount,
        PolicyPaymentMethodEnum $method,
        ?string $firstDueDate = null
    ): array {
        if ($installmentsCount < 1) {
            $installmentsCount = 1;
        }

        $perInstallment = round($totalPremium / $installmentsCount, 2);

        $base = $firstDueDate !== null
            ? \Carbon\Carbon::parse($firstDueDate)
            : now()->addMonth();

        $schedule = [];

        for ($i = 1; $i <= $installmentsCount; $i++) {
            $schedule[] = [
                'installment' => $i,
                'due_date'    => $base->copy()->addMonths($i - 1)->format('Y-m-d'),
                'amount'      => $i === $installmentsCount
                    ? round($totalPremium - ($perInstallment * ($installmentsCount - 1)), 2)
                    : $perInstallment,
                'status'      => 'pending',
                'method'       => $method->value,
            ];
        }

        return $schedule;
    }

    /*
    |--------------------------------------------------------------------------
    | Resolução de Tenant
    |--------------------------------------------------------------------------
    */

    /**
     * @throws ModelNotFoundException|RuntimeException
     */
    private function resolveTenantId(): int
    {
        $tenantId = auth()->user()?->tenant_id;

        if ($tenantId === null) {
            throw new RuntimeException('Não foi possível resolver o tenant do usuário autenticado.');
        }

        return (int) $tenantId;
    }
}
