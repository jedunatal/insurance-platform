<?php

namespace App\Models;

use App\Enums\ClaimStatusEnum;
use App\Enums\ClaimTypeEnum;
use App\Models\Traits\BelongsToTenant;
use App\Support\CurrencyHelper;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Claim extends Model
{
    use SoftDeletes;
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'policy_id',
        'insured_id',
        'created_by',
        'claim_number',
        'protocol_number',
        'insurer_claim_number',
        'claim_type',
        'status',
        'occurrence_date',
        'report_date',
        'estimated_amount',
        'indemnified_amount',
        'deductible_amount',
        'occurrence_description',
        'location',
        'third_party_details',
        'notes',
    ];

    protected $casts = [
        'status' => ClaimStatusEnum::class,
        'claim_type' => ClaimTypeEnum::class,
        'occurrence_date' => 'datetime',
        'report_date' => 'datetime',
        'third_party_details' => 'array',
        'estimated_amount' => 'decimal:2',
        'indemnified_amount' => 'decimal:2',
        'deductible_amount' => 'decimal:2',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function policy(): BelongsTo
    {
        return $this->belongsTo(Policy::class);
    }

    public function insured(): BelongsTo
    {
        return $this->belongsTo(Insured::class);
    }

    public function attachments(): \Illuminate\Database\Eloquent\Relations\MorphMany
    {
        return $this->morphMany(Attachment::class, 'attachable');
    }

    public function documents(): \Illuminate\Database\Eloquent\Relations\MorphMany
    {
        return $this->morphMany(Document::class, 'documentable');
    }

    /*
    |--------------------------------------------------------------------------
    | Mutators para Formatação Monetária
    |--------------------------------------------------------------------------
    */

    protected function estimatedAmount(): Attribute
    {
        return Attribute::make(
            set: fn (mixed $value) => \App\Support\CurrencyHelper::parse($value)
        );
    }

    protected function deductibleAmount(): Attribute
    {
        return Attribute::make(
            set: fn (mixed $value) => \App\Support\CurrencyHelper::parse($value)
        );
    }

    protected function indemnifiedAmount(): Attribute
    {
        return Attribute::make(
            set: fn (mixed $value) => \App\Support\CurrencyHelper::parse($value)
        );
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function formattedEstimatedAmount(): string
    {
        return 'R$ ' . number_format((float) $this->estimated_amount, 2, ',', '.');
    }

    public function formattedIndemnifiedAmount(): string
    {
        return 'R$ ' . number_format((float) $this->indemnified_amount, 2, ',', '.');
    }

    public function formattedDeductibleAmount(): string
    {
        return 'R$ ' . number_format((float) $this->deductible_amount, 2, ',', '.');
    }
}