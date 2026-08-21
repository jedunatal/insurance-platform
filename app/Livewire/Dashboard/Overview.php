<?php

namespace App\Livewire\Dashboard;

use App\Services\CRM\DashboardService;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Painel de Controle')]
#[Layout('layouts.app')]
class Overview extends Component
{
    #[On('policy-created')]
    #[On('policy-updated')]
    #[On('policy-renewed')]
    #[On('claim-created')]
    #[On('claim-updated')]
    #[On('lead-created')]
    #[On('lead-updated')]
    #[On('refresh-dashboard')]
    public function refreshData(): void
    {
        // Re-renders the component with freshly queried metrics
    }

    public function render(DashboardService $service)
    {
        return view('livewire.dashboard.overview', [
            'metrics'               => $service->getMetrics(),
            'criticalRenewals'      => $service->getCriticalRenewals(days: 30, limit: 6),
            'criticalRenewalsCount' => $service->getCriticalRenewalsCount(days: 30),
            'branchDistribution'    => $service->getBranchDistribution(),
            'insurerDistribution'   => $service->getInsurerDistribution(),
            'leadFunnel'            => $service->getLeadFunnel(),
            'recentPolicies'        => $service->getRecentPolicies(limit: 5),
            'recentClaims'          => $service->getRecentClaims(limit: 5),
            'recentLeads'           => $service->getRecentLeads(limit: 5),
        ]);
    }
}
