<?php

namespace App\Models;

use App\Enums\ClaimStatusEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Claim extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'tenant_id',
        'policy_id',
        'insured_id',
        'created_by',
        'claim_number',
        'insurer_claim_number',
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

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}