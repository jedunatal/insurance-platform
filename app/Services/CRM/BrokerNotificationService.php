<?php

namespace App\Services\CRM;

use App\Enums\ClaimStatusEnum;
use App\Enums\FinancialStatusEnum;
use App\Enums\LeadStatusEnum;
use App\Enums\PolicyStatusEnum;
use App\Enums\RenewalStageEnum;
use App\Models\Claim;
use App\Models\Lead;
use App\Models\Policy;
use App\Models\PolicyInstallment;
use App\Models\PolicyRenewal;

class BrokerNotificationService
{
    /**
     * Coleta todos os alertas operacionais urgentes do corretor para a central de notificações.
     *
     * @return array{total_unread: int, alerts: array<int, array<string, mixed>>}
     */
    public function getBrokerAlerts(): array
    {
        $tenantId = auth()->user()?->tenant_id;

        // 1. Apólices vencendo nos próximos 30 dias
        $expiringPolicies = Policy::query()
            ->when($tenantId, fn ($q) => $q->where('tenant_id', $tenantId))
            ->whereIn('status', [PolicyStatusEnum::Active->value, 'Ativa'])
            ->whereNotNull('end_date')
            ->whereBetween('end_date', [now(), now()->addDays(30)])
            ->count();

        // 2. Parcelas em Atraso
        $overdueInstallments = PolicyInstallment::query()
            ->when($tenantId, fn ($q) => $q->where('tenant_id', $tenantId))
            ->where(function ($q) {
                $q->where('status', FinancialStatusEnum::Overdue->value)
                  ->orWhere(function ($sub) {
                      $sub->where('status', FinancialStatusEnum::Pending->value)
                          ->where('due_date', '<', today());
                  });
            })
            ->count();

        // 3. Renovações Pendentes a Contatar
        $pendingRenewals = PolicyRenewal::query()
            ->when($tenantId, fn ($q) => $q->where('tenant_id', $tenantId))
            ->where('stage', RenewalStageEnum::ToContact->value)
            ->count();

        // 4. Sinistros em Aberto ou Análise
        $openClaims = Claim::query()
            ->when($tenantId, fn ($q) => $q->where('tenant_id', $tenantId))
            ->whereIn('status', [ClaimStatusEnum::Reported->value, ClaimStatusEnum::UnderAnalysis->value, 'reported', 'under_analysis'])
            ->count();

        // 5. Leads Novos aguardando primeiro contato
        $newLeads = Lead::query()
            ->when($tenantId, fn ($q) => $q->where('tenant_id', $tenantId))
            ->where('status', LeadStatusEnum::New->value)
            ->count();

        $alerts = [];

        if ($expiringPolicies > 0) {
            $alerts[] = [
                'type'        => 'expiring_policies',
                'title'       => "{$expiringPolicies} Apólice(s) a Vencer",
                'description' => "Contratos com vigência expirando nos próximos 30 dias.",
                'icon'        => 'heroicon-o-clock',
                'color'       => 'warning',
                'url'         => route('renewals.index'),
                'count'       => $expiringPolicies,
            ];
        }

        if ($overdueInstallments > 0) {
            $alerts[] = [
                'type'        => 'overdue_installments',
                'title'       => "{$overdueInstallments} Parcela(s) em Atraso",
                'description' => "Cobranças com data de vencimento expirada.",
                'icon'        => 'heroicon-o-exclamation-circle',
                'color'       => 'danger',
                'url'         => route('financial.index'),
                'count'       => $overdueInstallments,
            ];
        }

        if ($pendingRenewals > 0) {
            $alerts[] = [
                'type'        => 'pending_renewals',
                'title'       => "{$pendingRenewals} Renovação(ões) a Iniciar",
                'description' => "Clientes aguardando contato para estudo de renovação.",
                'icon'        => 'heroicon-o-arrow-path',
                'color'       => 'primary',
                'url'         => route('renewals.index'),
                'count'       => $pendingRenewals,
            ];
        }

        if ($openClaims > 0) {
            $alerts[] = [
                'type'        => 'open_claims',
                'title'       => "{$openClaims} Sinistro(s) em Regulação",
                'description' => "Processos de sinistro em andamento com as seguradoras.",
                'icon'        => 'heroicon-o-shield-exclamation',
                'color'       => 'amber',
                'url'         => route('claims.index'),
                'count'       => $openClaims,
            ];
        }

        if ($newLeads > 0) {
            $alerts[] = [
                'type'        => 'new_leads',
                'title'       => "{$newLeads} Novo(s) Lead(s)",
                'description' => "Oportunidades recentes aguardando qualificação.",
                'icon'        => 'heroicon-o-user-plus',
                'color'       => 'info',
                'url'         => route('leads.index'),
                'count'       => $newLeads,
            ];
        }

        $totalUnread = array_sum(array_column($alerts, 'count'));

        return [
            'total_unread' => $totalUnread,
            'alerts'       => $alerts,
        ];
    }
}
