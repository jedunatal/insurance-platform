<?php

namespace App\Services\CRM;

use App\Enums\ClaimStatusEnum;
use App\Enums\InsuranceBranchEnum;
use App\Enums\LeadStatusEnum;
use App\Enums\PolicyStatusEnum;
use App\Models\Claim;
use App\Models\Insured;
use App\Models\Lead;
use App\Models\Policy;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DashboardService
{
    /**
     * Retorna os 4 KPIs principais e métricas consolidadas do topo do Dashboard.
     */
    public function getMetrics(?int $tenantId = null): array
    {
        $tenantId ??= auth()->user()?->tenant_id;

        // 1. Leads e Conversão
        $leadsQuery = Lead::query()->when($tenantId, fn ($q) => $q->where('tenant_id', $tenantId));
        $totalLeads = (clone $leadsQuery)->count();
        $monthLeads = (clone $leadsQuery)->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->count();
        $convertedLeads = (clone $leadsQuery)->where(function ($q) {
            $q->where('status', LeadStatusEnum::Converted->value)
              ->orWhere('status', 'Convertido');
        })->count();

        $conversionRate = $totalLeads > 0 
            ? round(($convertedLeads / $totalLeads) * 100, 1) 
            : 0.0;

        // 2. Segurados Ativos
        $insuredsQuery = Insured::query()->when($tenantId, fn ($q) => $q->where('tenant_id', $tenantId));
        $totalInsureds = (clone $insuredsQuery)->count();
        $insuredsWithActivePolicies = (clone $insuredsQuery)
            ->whereHas('policies', fn ($q) => $q->where('status', PolicyStatusEnum::Active->value)->orWhere('status', 'active'))
            ->count();

        // 3. Apólices e Carteira Ativa
        $policiesQuery = Policy::query()->when($tenantId, fn ($q) => $q->where('tenant_id', $tenantId));
        $activePoliciesQuery = (clone $policiesQuery)->where(function ($q) {
            $q->where('status', PolicyStatusEnum::Active->value)
              ->orWhere('status', 'active');
        });
        $activePoliciesCount = (clone $activePoliciesQuery)->count();
        $totalActivePremium = (float) (clone $activePoliciesQuery)->sum('total_premium');

        // 4. Sinistros em Aberto e Prejuízo Estimado
        $closedStatuses = [
            ClaimStatusEnum::Indemnified->value,
            ClaimStatusEnum::Cancelled->value,
            ClaimStatusEnum::Rejected->value,
            'indemnified',
            'cancelled',
            'closed',
            'rejected',
            'Indenizado',
            'Cancelado',
            'Encerrado',
            'Recusado',
        ];

        $claimsQuery = Claim::query()->when($tenantId, fn ($q) => $q->where('tenant_id', $tenantId));
        $openClaimsQuery = (clone $claimsQuery)->whereNotIn('status', $closedStatuses);
        $openClaimsCount = (clone $openClaimsQuery)->count();
        $estimatedLoss = (float) (clone $openClaimsQuery)->sum('estimated_amount');
        $totalIndemnified = (float) (clone $claimsQuery)->sum('indemnified_amount');

        // Sinistralidade (Loss Ratio)
        $lossRatio = $totalActivePremium > 0 
            ? round(($totalIndemnified / $totalActivePremium) * 100, 1) 
            : 0.0;

        return [
            // KPIs de Topo
            'month_leads'            => $monthLeads,
            'total_leads'            => $totalLeads,
            'converted_leads'        => $convertedLeads,
            'conversion_rate'        => $conversionRate,
            'active_insureds'        => $totalInsureds,
            'insureds_with_policies' => $insuredsWithActivePolicies,
            'active_policies'        => $activePoliciesCount,
            'total_active_premium'   => $totalActivePremium,
            'open_claims'            => $openClaimsCount,
            'estimated_loss'         => $estimatedLoss,
            'total_indemnified'      => $totalIndemnified,
            'loss_ratio'             => $lossRatio,
            // Chave legada para compatibilidade com outros componentes
            'pipeline_leads'         => $totalLeads - $convertedLeads,
        ];
    }

    /**
     * Lista apólices ativas que expiram nos próximos $days dias (Foco em renovações).
     */
    public function getCriticalRenewals(int $days = 30, int $limit = 6, ?int $tenantId = null): Collection
    {
        $tenantId ??= auth()->user()?->tenant_id;

        return Policy::with(['insured', 'product'])
            ->when($tenantId, fn ($q) => $q->where('tenant_id', $tenantId))
            ->where(function ($q) {
                $q->where('status', PolicyStatusEnum::Active->value)
                  ->orWhere('status', 'active');
            })
            ->whereBetween('end_date', [now()->startOfDay(), now()->addDays($days)->endOfDay()])
            ->orderBy('end_date', 'asc')
            ->limit($limit)
            ->get();
    }

    /**
     * Total de contratos com renovação crítica nos próximos $days dias.
     */
    public function getCriticalRenewalsCount(int $days = 30, ?int $tenantId = null): int
    {
        $tenantId ??= auth()->user()?->tenant_id;

        return Policy::query()
            ->when($tenantId, fn ($q) => $q->where('tenant_id', $tenantId))
            ->where(function ($q) {
                $q->where('status', PolicyStatusEnum::Active->value)
                  ->orWhere('status', 'active');
            })
            ->whereBetween('end_date', [now()->startOfDay(), now()->addDays($days)->endOfDay()])
            ->count();
    }

    /**
     * Distribuição de Apólices Ativas por Ramo de Seguro.
     *
     * @return array<int, array{branch: string, label: string, count: int, total_premium: float, percentage: float}>
     */
    public function getBranchDistribution(?int $tenantId = null): array
    {
        if (! Schema::hasColumn('policies', 'branch')) {
            return [];
        }

        $tenantId ??= auth()->user()?->tenant_id;

        $results = Policy::query()
            ->when($tenantId, fn ($q) => $q->where('tenant_id', $tenantId))
            ->where(function ($q) {
                $q->where('status', PolicyStatusEnum::Active->value)
                  ->orWhere('status', 'active');
            })
            ->whereNotNull('branch')
            ->select('branch', DB::raw('COUNT(*) as total_count'), DB::raw('SUM(total_premium) as sum_premium'))
            ->groupBy('branch')
            ->orderByDesc('total_count')
            ->get();

        $totalPolicies = $results->sum('total_count');

        return $results->map(function ($row) use ($totalPolicies) {
            $branchValue = (string) $row->branch;
            $enumCase = InsuranceBranchEnum::tryFrom($branchValue);

            return [
                'branch'        => $branchValue,
                'label'         => $enumCase ? $enumCase->getLabel() : $branchValue,
                'count'         => (int) $row->total_count,
                'total_premium' => (float) $row->sum_premium,
                'percentage'    => $totalPolicies > 0 ? round(((int) $row->total_count / $totalPolicies) * 100, 1) : 0.0,
            ];
        })->values()->all();
    }

    /**
     * Distribuição de Apólices Ativas por Seguradora.
     *
     * @return array<int, array{insurer: string, count: int, total_premium: float, percentage: float}>
     */
    public function getInsurerDistribution(?int $tenantId = null): array
    {
        if (! Schema::hasColumn('policies', 'insurer')) {
            return [];
        }

        $tenantId ??= auth()->user()?->tenant_id;

        $results = Policy::query()
            ->when($tenantId, fn ($q) => $q->where('tenant_id', $tenantId))
            ->where(function ($q) {
                $q->where('status', PolicyStatusEnum::Active->value)
                  ->orWhere('status', 'active');
            })
            ->whereNotNull('insurer')
            ->where('insurer', '!=', '')
            ->select('insurer', DB::raw('COUNT(*) as total_count'), DB::raw('SUM(total_premium) as sum_premium'))
            ->groupBy('insurer')
            ->orderByDesc('total_count')
            ->limit(5)
            ->get();

        $totalPolicies = Policy::query()
            ->when($tenantId, fn ($q) => $q->where('tenant_id', $tenantId))
            ->where(function ($q) {
                $q->where('status', PolicyStatusEnum::Active->value)
                  ->orWhere('status', 'active');
            })
            ->count();

        return $results->map(function ($row) use ($totalPolicies) {
            return [
                'insurer'       => (string) $row->insurer,
                'count'         => (int) $row->total_count,
                'total_premium' => (float) $row->sum_premium,
                'percentage'    => $totalPolicies > 0 ? round(((int) $row->total_count / $totalPolicies) * 100, 1) : 0.0,
            ];
        })->values()->all();
    }

    /**
     * Funil de Leads por status.
     *
     * @return array<int, array{status: string, label: string, color: string, badge_class: string, count: int, percentage: float}>
     */
    public function getLeadFunnel(?int $tenantId = null): array
    {
        $tenantId ??= auth()->user()?->tenant_id;

        $leadsByStatus = Lead::query()
            ->when($tenantId, fn ($q) => $q->where('tenant_id', $tenantId))
            ->select('status', DB::raw('COUNT(*) as total_count'))
            ->groupBy('status')
            ->pluck('total_count', 'status')
            ->all();

        $totalLeads = array_sum($leadsByStatus);

        $funnel = [];
        foreach (LeadStatusEnum::cases() as $case) {
            $count = (int) ($leadsByStatus[$case->value] ?? $leadsByStatus[strtolower($case->value)] ?? 0);
            $percentage = $totalLeads > 0 ? round(($count / $totalLeads) * 100, 1) : 0.0;

            $funnel[] = [
                'status'      => $case->value,
                'label'       => $case->getLabel(),
                'color'       => $case->getColor(),
                'badge_class' => $case->badgeClasses(),
                'count'       => $count,
                'percentage'  => $percentage,
            ];
        }

        return $funnel;
    }

    /**
     * Últimas 5 apólices emitidas na plataforma.
     */
    public function getRecentPolicies(int $limit = 5, ?int $tenantId = null): Collection
    {
        $tenantId ??= auth()->user()?->tenant_id;

        return Policy::with(['insured', 'product', 'broker'])
            ->when($tenantId, fn ($q) => $q->where('tenant_id', $tenantId))
            ->latest('created_at')
            ->limit($limit)
            ->get();
    }

    /**
     * Últimos 5 sinistros registrados na plataforma.
     */
    public function getRecentClaims(int $limit = 5, ?int $tenantId = null): Collection
    {
        $tenantId ??= auth()->user()?->tenant_id;

        return Claim::with(['insured', 'policy'])
            ->when($tenantId, fn ($q) => $q->where('tenant_id', $tenantId))
            ->latest('created_at')
            ->limit($limit)
            ->get();
    }

    /**
     * Últimos 5 leads cadastrados no funil.
     */
    public function getRecentLeads(int $limit = 5, ?int $tenantId = null): Collection
    {
        $tenantId ??= auth()->user()?->tenant_id;

        return Lead::with('product')
            ->when($tenantId, fn ($q) => $q->where('tenant_id', $tenantId))
            ->latest('created_at')
            ->limit($limit)
            ->get();
    }
}