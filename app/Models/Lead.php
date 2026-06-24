<?php

namespace App\Models;

use App\Enums\LeadSourceEnum;
use App\Enums\LeadStatusEnum;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Lead extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'tenant_id',
        'assigned_to',
        'created_by',
        'name',
        'email',
        'phone',
        'source',
        'status',
        'next_contact_at',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'source' => LeadSourceEnum::class,
            'status' => LeadStatusEnum::class,
            'next_contact_at' => 'immutable_datetime',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /*
    |--------------------------------------------------------------------------
    | Query Scopes
    |--------------------------------------------------------------------------
    */

    public function scopeForTenant(Builder $query, int $tenantId): Builder
    {
        return $query->where('tenant_id', $tenantId);
    }

    public function scopeByStatus(
        Builder $query,
        LeadStatusEnum $status
    ): Builder {
        return $query->where('status', $status->value);
    }

    public function scopeByConsultant(
        Builder $query,
        int $userId
    ): Builder {
        return $query->where('assigned_to', $userId);
    }

    public function scopeSearch(
        Builder $query,
        string $term
    ): Builder {
        $term = "%{$term}%";

        return $query->where(
            static function (Builder $query) use ($term): void {
                $query
                    ->where('name', 'like', $term)
                    ->orWhere('email', 'like', $term)
                    ->orWhere('phone', 'like', $term);
            }
        );
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->whereNotIn('status', [
            LeadStatusEnum::Converted->value,
            LeadStatusEnum::Lost->value,
        ]);
    }

    public function scopeOverdue(Builder $query): Builder
    {
        return $query
            ->active()
            ->whereNotNull('next_contact_at')
            ->where('next_contact_at', '<', now());
    }

    public function scopeCreatedToday(Builder $query): Builder
    {
        return $query->whereDate('created_at', today());
    }
}