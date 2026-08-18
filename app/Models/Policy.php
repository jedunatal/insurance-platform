<?php

namespace App\Models;

use App\Enums\PolicyPaymentMethodEnum;
use App\Enums\PolicyStatusEnum;
use App\Models\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Arr;

class Policy extends Model
{
    use HasFactory;
    use SoftDeletes;
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'insured_id',
        'product_id',
        'broker_id',
        'created_by',
        'policy_number',
        'proposal_number',
        'branch_code',
        'susep_process',
        'ci_code',
        'status',
        'start_date',
        'end_date',
        'insured_object',
        'coverages',
        'net_premium',
        'iof_amount',
        'total_premium',
        'payment_method',
        'installments_count',
        'installments_schedule',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'start_date'          => 'datetime',
            'end_date'           => 'datetime',
            'insured_object'      => 'array',
            'coverages'           => 'array',
            'installments_schedule' => 'array',
            'net_premium'         => 'decimal:2',
            'iof_amount'          => 'decimal:2',
            'total_premium'       => 'decimal:2',
            'installments_count'  => 'integer',
            'status'              => PolicyStatusEnum::class,
            'payment_method'      => PolicyPaymentMethodEnum::class,
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Relacionamentos (Relationships)
    |--------------------------------------------------------------------------
    */

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function insured(): BelongsTo
    {
        return $this->belongsTo(Insured::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function broker(): BelongsTo
    {
        return $this->belongsTo(User::class, 'broker_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /*
    |--------------------------------------------------------------------------
    | Escopos de Consulta (Query Scopes)
    |--------------------------------------------------------------------------
    */

    public function scopeForTenant(Builder $query, int $tenantId): Builder
    {
        return $query->where('tenant_id', $tenantId);
    }

    public function scopeByBroker(Builder $query, int $userId): Builder
    {
        return $query->where('broker_id', $userId);
    }

    public function scopeByInsured(Builder $query, int $insuredId): Builder
    {
        return $query->where('insured_id', $insuredId);
    }

    public function scopeByProduct(Builder $query, int $productId): Builder
    {
        return $query->where('product_id', $productId);
    }

    public function scopeByStatus(Builder $query, PolicyStatusEnum $status): Builder
    {
        return $query->where('status', $status->value);
    }

    public function scopeDraft(Builder $query): Builder
    {
        return $this->scopeByStatus($query, PolicyStatusEnum::Draft);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $this->scopeByStatus($query, PolicyStatusEnum::Active);
    }

    public function scopePendingRenewal(Builder $query): Builder
    {
        return $this->scopeByStatus($query, PolicyStatusEnum::PendingRenewal);
    }

    public function scopeRenewed(Builder $query): Builder
    {
        return $this->scopeByStatus($query, PolicyStatusEnum::Renewed);
    }

    public function scopeCancelled(Builder $query): Builder
    {
        return $this->scopeByStatus($query, PolicyStatusEnum::Cancelled);
    }

    public function scopeExpired(Builder $query): Builder
    {
        return $this->scopeByStatus($query, PolicyStatusEnum::Expired);
    }

    public function scopeExpiringSoon(Builder $query, int $days = 30): Builder
    {
        return $query
            ->whereNotNull('end_date')
            ->whereBetween('end_date', [now(), now()->addDays($days)]);
    }

    public function scopeSearch(Builder $query, string $term): Builder
    {
        $term = "%{$term}%";

        return $query->where(
            static function (Builder $query) use ($term): void {
                $query
                    ->where('policy_number', 'like', $term)
                    ->orWhere('proposal_number', 'like', $term)
                    ->orWhere('susep_process', 'like', $term)
                    ->orWhere('ci_code', 'like', $term)
                    ->orWhere('notes', 'like', $term);
            }
        );
    }

    public function scopeCreatedToday(Builder $query): Builder
    {
        return $query->whereDate('created_at', today());
    }

    /*
    |--------------------------------------------------------------------------
    | Acessores de Apresentação
    |--------------------------------------------------------------------------
    */

    public function isEditable(): bool
    {
        return $this->status instanceof PolicyStatusEnum
            ? $this->status->isEditable()
            : PolicyStatusEnum::try($this->getRawOriginal('status'))?->isEditable() ?? false;
    }

    public function isDeletable(): bool
    {
        return $this->status instanceof PolicyStatusEnum
            ? $this->status->isDeletable()
            : PolicyStatusEnum::try($this->getRawOriginal('status'))?->isDeletable() ?? false;
    }

    public function isExpired(): bool
    {
        return $this->end_date !== null && $this->end_date->isPast();
    }

    public function formattedNetPremium(): string
    {
        return 'R$ ' . number_format((float) $this->net_premium, 2, ',', '.');
    }

    public function formattedTotalPremium(): string
    {
        return 'R$ ' . number_format((float) $this->total_premium, 2, ',', '.');
    }

    /**
     * Resumo do objeto segurado para exibição rápida.
     */
    public function insuredObjectSummary(): string
    {
        if (! is_array($this->insured_object) || blank($this->insured_object)) {
            return '-';
        }

        $parts = array_filter([
            Arr::get($this->insured_object, 'marca'),
            Arr::get($this->insured_object, 'modelo'),
        ]);

        return $parts !== [] ? implode(' ', $parts) : (Arr::get($this->insured_object, 'equipamento') ?? '-');
    }
}
