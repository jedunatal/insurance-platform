<?php

namespace App\Livewire\Layout;

use App\Models\Claim;
use App\Models\Insured;
use App\Models\Lead;
use App\Models\Policy;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class GlobalSearch extends Component
{
    public string $query = '';

    public bool $isOpen = false;

    public function updatedQuery(): void
    {
        $this->isOpen = strlen(trim($this->query)) >= 2;
    }

    public function openSearch(): void
    {
        if (strlen(trim($this->query)) >= 2) {
            $this->isOpen = true;
        }
    }

    public function closeSearch(): void
    {
        $this->isOpen = false;
    }

    public function clearSearch(): void
    {
        $this->query = '';
        $this->isOpen = false;
    }

    /**
     * Executa a busca multidomínio otimizada nos 4 módulos.
     *
     * @return array{leads: \Illuminate\Support\Collection, insureds: \Illuminate\Support\Collection, policies: \Illuminate\Support\Collection, claims: \Illuminate\Support\Collection, totalCount: int}
     */
    public function getSearchResults(): array
    {
        $term = trim($this->query);

        if (strlen($term) < 2) {
            return [
                'leads'      => collect(),
                'insureds'   => collect(),
                'policies'   => collect(),
                'claims'     => collect(),
                'totalCount' => 0,
            ];
        }

        $tenantId = auth()->user()?->tenant_id;
        $cleanDigits = preg_replace('/\D/', '', $term);

        $formattedCpf = (strlen($cleanDigits) === 11)
            ? vsprintf('%s%s%s.%s%s%s.%s%s%s-%s%s', str_split($cleanDigits))
            : null;

        $formattedCnpj = (strlen($cleanDigits) === 14)
            ? vsprintf('%s%s.%s%s%s.%s%s%s/%s%s%s%s-%s%s', str_split($cleanDigits))
            : null;

        // 1. Leads
        $leads = Lead::query()
            ->when($tenantId, fn ($q) => $q->where('tenant_id', $tenantId))
            ->where(function ($q) use ($term, $cleanDigits, $formattedCpf, $formattedCnpj) {
                $q->where('name', 'like', "%{$term}%")
                  ->orWhere('email', 'like', "%{$term}%")
                  ->orWhere('notes', 'like', "%{$term}%");

                if (! empty($cleanDigits) && strlen($cleanDigits) >= 3) {
                    $q->orWhere('phone', 'like', "%{$cleanDigits}%")
                      ->orWhere('document', 'like', "%{$cleanDigits}%");

                    if ($formattedCpf) {
                        $q->orWhere('document', 'like', "%{$formattedCpf}%");
                    }
                    if ($formattedCnpj) {
                        $q->orWhere('document', 'like', "%{$formattedCnpj}%");
                    }
                }
            })
            ->with('product')
            ->latest('created_at')
            ->take(4)
            ->get();

        // 2. Segurados (Insured)
        $insureds = Insured::query()
            ->when($tenantId, fn ($q) => $q->where('tenant_id', $tenantId))
            ->where(function ($q) use ($term, $cleanDigits, $formattedCpf, $formattedCnpj) {
                $q->where('name', 'like', "%{$term}%")
                  ->orWhere('email', 'like', "%{$term}%")
                  ->orWhere('city', 'like', "%{$term}%")
                  ->orWhere('document', 'like', "%{$term}%");

                if (! empty($cleanDigits) && strlen($cleanDigits) >= 3) {
                    $q->orWhere('document', 'like', "%{$cleanDigits}%")
                      ->orWhere('phone', 'like', "%{$cleanDigits}%");

                    if ($formattedCpf) {
                        $q->orWhere('document', 'like', "%{$formattedCpf}%");
                    }
                    if ($formattedCnpj) {
                        $q->orWhere('document', 'like', "%{$formattedCnpj}%");
                    }
                }
            })
            ->latest('created_at')
            ->take(4)
            ->get();

        // 3. Apólices (Policy)
        $policies = Policy::query()
            ->when($tenantId, fn ($q) => $q->where('tenant_id', $tenantId))
            ->where(function ($q) use ($term) {
                $q->where('policy_number', 'like', "%{$term}%")
                  ->orWhere('proposal_number', 'like', "%{$term}%")
                  ->orWhere('insurer', 'like', "%{$term}%")
                  ->orWhere('branch', 'like', "%{$term}%")
                  ->orWhereHas('insured', fn ($sub) => $sub->where('name', 'like', "%{$term}%"));
            })
            ->with('insured')
            ->latest('created_at')
            ->take(4)
            ->get();

        // 4. Sinistros (Claim)
        $claims = Claim::query()
            ->when($tenantId, fn ($q) => $q->where('tenant_id', $tenantId))
            ->where(function ($q) use ($term) {
                $q->where('claim_number', 'like', "%{$term}%")
                  ->orWhere('protocol_number', 'like', "%{$term}%")
                  ->orWhere('claim_type', 'like', "%{$term}%")
                  ->orWhereHas('insured', fn ($sub) => $sub->where('name', 'like', "%{$term}%"));
            })
            ->with(['insured', 'policy'])
            ->latest('created_at')
            ->take(4)
            ->get();

        $totalCount = $leads->count() + $insureds->count() + $policies->count() + $claims->count();

        return [
            'leads'      => $leads,
            'insureds'   => $insureds,
            'policies'   => $policies,
            'claims'     => $claims,
            'totalCount' => $totalCount,
        ];
    }

    public function render(): View
    {
        return view('livewire.layout.global-search', [
            'searchResults' => $this->getSearchResults(),
        ]);
    }
}
