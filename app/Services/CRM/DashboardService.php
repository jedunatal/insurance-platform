<?php

namespace App\Services\CRM;

use App\Enums\PolicyStatusEnum;
use App\Models\Claim;
use App\Models\Insured;
use App\Models\Lead;
use App\Models\Policy;
use Illuminate\Database\Eloquent\Collection;

class DashboardService
{
    public function getMetrics(): array
    {
        return [
            'active_insureds' => Insured::count(),
            'pipeline_leads' => Lead::whereIn('status', ['novo', 'contato', 'proposta'])->count(),
            'active_policies' => Policy::where('status', PolicyStatusEnum::Active)->count(),
            'total_active_premium' => (float) Policy::where('status', PolicyStatusEnum::Active)->sum('total_premium'),
            'open_claims' => Claim::whereNotIn('status', ['indemnified', 'cancelled'])->count(),
        ];
    }

    public function getCriticalRenewals(int $days = 30, int $limit = 5): Collection
    {
        return Policy::with(['insured', 'product'])
            ->where('status', PolicyStatusEnum::Active)
            ->whereBetween('end_date', [now(), now()->addDays($days)])
            ->orderBy('end_date', 'asc')
            ->limit($limit)
            ->get();
    }

    public function getRecentClaims(int $limit = 5): Collection
    {
        return Claim::with(['insured', 'policy'])
            ->latest('occurrence_date')
            ->limit($limit)
            ->get();
    }

    public function getRecentLeads(int $limit = 5): Collection
    {
        return Lead::with('product')
            ->latest()
            ->limit($limit)
            ->get();
    }
}