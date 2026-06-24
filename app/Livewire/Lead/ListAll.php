<?php

namespace App\Livewire\Lead;

use App\Enums\LeadStatusEnum;
use App\Services\CRM\LeadService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Leads')]
#[Layout('layouts.app')]
final class ListAll extends Component 
{
    use WithPagination;

    #[Url(as: 'q')]
    public string $search = '';

    #[Url(as: 'status')]
    public string $statusFilter = '';

    /*
    |--------------------------------------------------------------------------
    | Watchers
    |--------------------------------------------------------------------------
    */

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedStatusFilter(): void
    {
        $this->resetPage();
    }

    /*
    |--------------------------------------------------------------------------
    | Computed
    |--------------------------------------------------------------------------
    */

    #[Computed]
    public function leads(): LengthAwarePaginator
    {
        $status = filled($this->statusFilter)
            ? LeadStatusEnum::from($this->statusFilter)
            : null;

        return app(LeadService::class)->paginate(
            search: filled($this->search)
                ? $this->search
                : null,

            status: $status,
        );
    }

    #[Computed]
    public function statuses(): array
    {
        return LeadStatusEnum::cases();
    }

    /*
    |--------------------------------------------------------------------------
    | Actions
    |--------------------------------------------------------------------------
    */

    public function clearFilters(): void
    {
        $this->search = '';

        $this->statusFilter = '';

        $this->resetPage();
    }

    public function confirmDelete(int $id): void
    {
        $this->dispatch(
            'confirm-delete',
            id: $id
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Render
    |--------------------------------------------------------------------------
    */

    public function render(): \Illuminate\View\View
    {
        return view('livewire.lead.list-all');
    }
}