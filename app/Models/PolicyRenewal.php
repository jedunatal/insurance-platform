<?php

namespace App\Models;

use App\Enums\RenewalLossReasonEnum;
use App\Enums\RenewalStageEnum;
use App\Models\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class PolicyRenewal extends Model
{
    use HasFactory, SoftDeletes, BelongsToTenant;

    protected $table = 'policy_renewals';

    protected $fillable = [
        'tenant_id',
        'policy_id',
        'insured_id',
        'renewed_policy_id',
        'assigned_to',
        'created_by',
        'stage',
        'loss_reason',
        'loss_notes',
        'target_date',
        'notes',
    ];

    protected $casts = [
        'stage'       => RenewalStageEnum::class,
        'loss_reason' => RenewalLossReasonEnum::class,
        'target_date' => 'date',
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

    public function renewedPolicy(): BelongsTo
    {
        return $this->belongsTo(Policy::class, 'renewed_policy_id');
    }

    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
