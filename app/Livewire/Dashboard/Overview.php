<?php

namespace App\Livewire\Dashboard;

use App\Services\CRM\DashboardService;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Dashboard Geral')]
#[Layout('layouts.app')]
class Overview extends Component
{
    public function render(DashboardService $service)
    {
        return view('livewire.dashboard.overview', [
            'metrics' => $service->getMetrics(),
            'criticalRenewals' => $service->getCriticalRenewals(),
            'recentClaims' => $service->getRecentClaims(),
            'recentLeads' => $service->getRecentLeads(),
        ]);
    }
}
