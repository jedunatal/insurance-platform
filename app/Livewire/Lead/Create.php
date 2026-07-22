<?php

namespace App\Livewire\Lead;

use App\Enums\LeadSourceEnum;
use App\Enums\LeadStatusEnum;
use App\Models\Lead;
use App\Models\Product;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Create extends Component
{
    public array $data = [
        'name' => '',
        'email' => '',
        'phone' => '',
        'document' => '',
        'product_id' => '',
        'source' => 'manual',
        'status' => 'novo',
        'next_contact_at' => null,
        'notes' => '',
    ];

    public function getStatusesProperty()
    {
        return LeadStatusEnum::cases();
    }

    public function getSourcesProperty()
    {
        return LeadSourceEnum::cases();
    }

    public function getProductsProperty()
    {
        return Product::query()->where('is_active', true)->get();
    }

    public function save(): void
    {
        $validated = $this->validate([
            'data.name' => 'required|string|max:255',
            'data.email' => 'nullable|email|max:255',
            'data.phone' => 'nullable|string|max:20',
            'data.document' => 'nullable|string|max:20',
            'data.product_id' => 'nullable|exists:products,id',
            'data.source' => 'required',
            'data.status' => 'required',
            'data.next_contact_at' => 'nullable',
            'data.notes' => 'nullable|string',
        ]);

        DB::transaction(function () use ($validated) {
            $payload = $validated['data'];
            $payload['tenant_id'] = auth()->user()->tenant_id ?? 1;
            $payload['created_by'] = auth()->id();

            Lead::create($payload);
        });

        $this->redirectRoute('leads.index');
    }

    public function render(): View
    {
        return view('livewire.lead.create');
    }
}