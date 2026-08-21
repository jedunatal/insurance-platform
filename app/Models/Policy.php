<?php

namespace App\Models;

use App\Enums\PolicyPaymentMethodEnum;
use App\Enums\PolicyStatusEnum;
use App\Enums\RenewalStageEnum;
use App\Models\Traits\BelongsToTenant;
use App\Support\CurrencyHelper;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
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
        'previous_policy_id',
        'product_id',
        'broker_id',
        'producer_id',
        'created_by',
        'policy_number',
        'proposal_number',
        'insurer',
        'branch',
        'branch_code',
        'susep_process',
        'ci_code',
        'status',
        'renewal_status',
        'start_date',
        'end_date',
        'insured_object',
        'vehicle_data',
        'property_data',
        'beneficiaries',
        'coverages',
        'net_premium',
        'iof_rate',
        'iof_amount',
        'total_premium',
        'commission_percentage',
        'commission_amount',
        'producer_commission_percentage',
        'producer_commission_amount',
        'deductible_amount',
        'payment_method',
        'installments_count',
        'installments_schedule',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'start_date'                     => 'datetime',
            'end_date'                       => 'datetime',
            'insured_object'                 => 'array',
            'vehicle_data'                   => 'array',
            'property_data'                  => 'array',
            'beneficiaries'                  => 'array',
            'coverages'                      => 'array',
            'installments_schedule'          => 'array',
            'net_premium'                    => 'decimal:2',
            'iof_rate'                       => 'decimal:2',
            'iof_amount'                     => 'decimal:2',
            'total_premium'                  => 'decimal:2',
            'commission_percentage'          => 'decimal:2',
            'commission_amount'              => 'decimal:2',
            'producer_commission_percentage' => 'decimal:2',
            'producer_commission_amount'     => 'decimal:2',
            'deductible_amount'              => 'decimal:2',
            'installments_count'             => 'integer',
            'renewal_status'                 => RenewalStageEnum::class,
            'payment_method'                 => PolicyPaymentMethodEnum::class,
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

    public function previousPolicy(): BelongsTo
    {
        return $this->belongsTo(Policy::class, 'previous_policy_id');
    }

    public function renewedPolicies(): HasMany
    {
        return $this->hasMany(Policy::class, 'previous_policy_id');
    }

    public function renewal(): HasOne
    {
        return $this->hasOne(PolicyRenewal::class, 'policy_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function broker(): BelongsTo
    {
        return $this->belongsTo(User::class, 'broker_id');
    }

    public function producer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'producer_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function claims(): HasMany
    {
        return $this->hasMany(Claim::class);
    }

    public function installments(): HasMany
    {
        return $this->hasMany(PolicyInstallment::class)->orderBy('installment_number');
    }

    public function attachments(): MorphMany
    {
        return $this->morphMany(Attachment::class, 'attachable');
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
            ->where(function ($q) {
                $q->where('status', PolicyStatusEnum::Active->value)
                  ->orWhere('status', 'active')
                  ->orWhere('status', 'Ativa');
            })
            ->whereNotNull('end_date')
            ->whereBetween('end_date', [now()->startOfDay(), now()->addDays($days)->endOfDay()]);
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

    public function formattedDeductibleAmount(): string
    {
        return 'R$ ' . number_format((float) $this->deductible_amount, 2, ',', '.');
    }

    public function formattedValidity(): string
    {
        if (! $this->start_date && ! $this->end_date) {
            return 'Vigência não informada';
        }

        $start = $this->start_date ? $this->start_date->format('d/m/Y') : 'Início não definido';
        $end = $this->end_date ? $this->end_date->format('d/m/Y') : 'Fim não definido';

        return "{$start} a {$end}";
    }

    /**
     * Resumo do objeto segurado para exibição rápida.
     */
    public function insuredObjectSummary(): string
    {
        if (! empty($this->vehicle_data) && is_array($this->vehicle_data)) {
            $brand = Arr::get($this->vehicle_data, 'brand') ?? Arr::get($this->vehicle_data, 'marca');
            $model = Arr::get($this->vehicle_data, 'model') ?? Arr::get($this->vehicle_data, 'modelo');
            $plate = Arr::get($this->vehicle_data, 'plate') ?? Arr::get($this->vehicle_data, 'placa');

            return trim("{$brand} {$model} " . ($plate ? "[{$plate}]" : ''));
        }

        if (! empty($this->property_data) && is_array($this->property_data)) {
            $type = Arr::get($this->property_data, 'property_type') ?? Arr::get($this->property_data, 'tipo_imovel');
            $city = Arr::get($this->property_data, 'city') ?? Arr::get($this->property_data, 'cidade');

            return trim("{$type} em {$city}");
        }

        if (! is_array($this->insured_object) || blank($this->insured_object)) {
            return '-';
        }

        $parts = array_filter([
            Arr::get($this->insured_object, 'marca'),
            Arr::get($this->insured_object, 'modelo'),
        ]);

        return $parts !== [] ? implode(' ', $parts) : (Arr::get($this->insured_object, 'equipamento') ?? '-');
    }

    /*
    |--------------------------------------------------------------------------
    | Mutators para Formatação Monetária
    |--------------------------------------------------------------------------
    */

    protected function netPremium(): Attribute
    {
        return Attribute::make(
            set: fn (mixed $value) => CurrencyHelper::parse($value)
        );
    }

    protected function iofAmount(): Attribute
    {
        return Attribute::make(
            set: fn (mixed $value) => CurrencyHelper::parse($value)
        );
    }

    protected function totalPremium(): Attribute
    {
        return Attribute::make(
            set: fn (mixed $value) => CurrencyHelper::parse($value)
        );
    }

    protected function commissionAmount(): Attribute
    {
        return Attribute::make(
            set: fn (mixed $value) => CurrencyHelper::parse($value)
        );
    }

    protected function producerCommissionAmount(): Attribute
    {
        return Attribute::make(
            set: fn (mixed $value) => CurrencyHelper::parse($value)
        );
    }

    protected function deductibleAmount(): Attribute
    {
        return Attribute::make(
            set: fn (mixed $value) => CurrencyHelper::parse($value)
        );
    }

    protected function status(): Attribute
    {
        return Attribute::make(
            get: fn (mixed $value) => $value instanceof PolicyStatusEnum ? $value : ($value ? PolicyStatusEnum::fromValue($value) : PolicyStatusEnum::Active),
            set: fn (mixed $value) => $value instanceof PolicyStatusEnum ? $value->value : ($value ? PolicyStatusEnum::fromValue($value)->value : PolicyStatusEnum::Active->value),
        );
    }
}
