<?php

namespace App\Models;

use App\Enums\InsuranceBranchEnum;
use App\Enums\QuoteStatusEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Quote extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'quotes';

    protected $fillable = [
        'tenant_id',
        'lead_id',
        'insured_id',
        'created_by',
        'converted_policy_id',
        'quote_number',
        'title',
        'branch',
        'status',
        'valid_until',
        'risk_data',
        'notes',
    ];

    protected $casts = [
        'status'      => QuoteStatusEnum::class,
        'branch'      => InsuranceBranchEnum::class,
        'valid_until' => 'date',
        'risk_data'   => 'array',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class);
    }

    public function insured(): BelongsTo
    {
        return $this->belongsTo(Insured::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function convertedPolicy(): BelongsTo
    {
        return $this->belongsTo(Policy::class, 'converted_policy_id');
    }

    public function options(): HasMany
    {
        return $this->hasMany(QuoteOption::class, 'quote_id');
    }

    public function attachments(): MorphMany
    {
        return $this->morphMany(Attachment::class, 'attachable');
    }

    /**
     * Retorna a melhor opção ou a recomendada.
     */
    public function recommendedOption(): ?QuoteOption
    {
        return $this->options()->where('is_recommended', true)->first()
            ?? $this->options()->orderBy('total_premium', 'asc')->first();
    }
}
