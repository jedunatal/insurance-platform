<?php

namespace App\Livewire\Renewal;

use App\Actions\Renewal\ClonePolicyForRenewalAction;
use App\Actions\Renewal\StartPolicyRenewalAction;
use App\Actions\Renewal\UpdateRenewalStageAction;
use App\Enums\PolicyStatusEnum;
use App\Enums\RenewalLossReasonEnum;
use App\Enums\RenewalStageEnum;
use App\Models\Policy;
use App\Models\PolicyRenewal;
use Filament\Notifications\Notification;
use Illuminate\Support\Collection;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Esteira de Renovações')]
#[Layout('layouts.app')]
class Pipeline extends Component
{
    public ?int $selectedRenewalId = null;

    public string $lossReason = 'price';

    public string $lossNotes = '';

    public bool $lossModalOpen = false;

    public function syncExpiringPolicies(): void
    {
        $tenantId = auth()->user()?->tenant_id;

        // Busca apólices ativas que vencem nos próximos 45 dias e ainda não estão na esteira
        $expiringPolicies = Policy::query()
            ->when($tenantId, fn ($q) => $q->where('tenant_id', $tenantId))
            ->whereIn('status', [PolicyStatusEnum::Active->value, 'Ativa'])
            ->whereNotNull('end_date')
            ->whereBetween('end_date', [now(), now()->addDays(45)])
            ->whereDoesntHave('renewal')
            ->get();

        $action = app(StartPolicyRenewalAction::class);
        $count = 0;

        foreach ($expiringPolicies as $policy) {
            $action->execute($policy);
            $count++;
        }

        Notification::make()
            ->title("Sincronização Concluída!")
            ->body("{$count} apólice(s) a vencer foram adicionadas à esteira de renovação.")
            ->success()
            ->send();
    }

    public function moveStage(int $renewalId, string $targetStage): void
    {
        $renewal = PolicyRenewal::findOrFail($renewalId);
        $stageEnum = RenewalStageEnum::from($targetStage);

        if ($stageEnum === RenewalStageEnum::Lost) {
            $this->selectedRenewalId = $renewalId;
            $this->lossModalOpen = true;

            return;
        }

        if ($stageEnum === RenewalStageEnum::Renewed && $renewal->policy) {
            $this->renewInOneClick($renewalId);

            return;
        }

        app(UpdateRenewalStageAction::class)->execute($renewal, $stageEnum);

        Notification::make()
            ->title("Estágio Atualizado")
            ->body("A renovação foi movida para {$stageEnum->getLabel()}.")
            ->info()
            ->send();
    }

    public function renewInOneClick(int $renewalId): void
    {
        $renewal = PolicyRenewal::with('policy')->findOrFail($renewalId);

        if (! $renewal->policy) {
            Notification::make()->title('Apólice original não encontrada!')->danger()->send();

            return;
        }

        $newPolicy = app(ClonePolicyForRenewalAction::class)->execute($renewal->policy);

        Notification::make()
            ->title('Apólice Renovada com Sucesso!')
            ->body("Nova apólice #{$newPolicy->policy_number} emitida para o próximo período com grade de parcelas gerada.")
            ->success()
            ->send();
    }

    public function confirmLoss(): void
    {
        if (! $this->selectedRenewalId) {
            return;
        }

        $renewal = PolicyRenewal::findOrFail($this->selectedRenewalId);
        $reasonEnum = RenewalLossReasonEnum::tryFrom($this->lossReason) ?? RenewalLossReasonEnum::Other;

        app(UpdateRenewalStageAction::class)->execute(
            $renewal,
            RenewalStageEnum::Lost,
            $reasonEnum,
            $this->lossNotes
        );

        $this->lossModalOpen = false;
        $this->selectedRenewalId = null;
        $this->lossNotes = '';

        Notification::make()
            ->title('Renovação Registrada como Não Renovada')
            ->body("Motivo registrado: {$reasonEnum->getLabel()}.")
            ->warning()
            ->send();
    }

    /**
     * @return array<string, Collection>
     */
    public function getColumnsProperty(): array
    {
        $tenantId = auth()->user()?->tenant_id;

        $renewals = PolicyRenewal::query()
            ->when($tenantId, fn ($q) => $q->where('tenant_id', $tenantId))
            ->with(['policy.insured', 'insured', 'assignedUser'])
            ->orderBy('target_date', 'asc')
            ->get();

        return [
            RenewalStageEnum::ToContact->value    => $renewals->where('stage', RenewalStageEnum::ToContact),
            RenewalStageEnum::InQuotation->value  => $renewals->where('stage', RenewalStageEnum::InQuotation),
            RenewalStageEnum::ProposalSent->value => $renewals->where('stage', RenewalStageEnum::ProposalSent),
            RenewalStageEnum::Renewed->value      => $renewals->where('stage', RenewalStageEnum::Renewed),
            RenewalStageEnum::Lost->value         => $renewals->where('stage', RenewalStageEnum::Lost),
        ];
    }

    public function render()
    {
        return view('livewire.renewal.pipeline', [
            'columns' => $this->columns,
            'stages'  => [
                RenewalStageEnum::ToContact,
                RenewalStageEnum::InQuotation,
                RenewalStageEnum::ProposalSent,
                RenewalStageEnum::Renewed,
                RenewalStageEnum::Lost,
            ],
        ]);
    }
}
