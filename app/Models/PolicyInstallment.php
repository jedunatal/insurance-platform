<?php

namespace App\Models;

use App\Enums\FinancialStatusEnum;
use App\Models\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class PolicyInstallment extends Model
{
    use HasFactory;
    use SoftDeletes;
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'policy_id',
        'insured_id',
        'installment_number',
        'total_installments',
        'due_date',
        'payment_date',
        'gross_amount',
        'commission_expected',
        'commission_received',
        'status',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'installment_number'  => 'integer',
            'total_installments'  => 'integer',
            'due_date'            => 'date',
            'payment_date'        => 'date',
            'gross_amount'        => 'decimal:2',
            'commission_expected' => 'decimal:2',
            'commission_received' => 'decimal:2',
            'status'              => FinancialStatusEnum::class,
        ];
    }

    public function policy(): BelongsTo
    {
        return $this->belongsTo(Policy::class);
    }

    public function insured(): BelongsTo
    {
        return $this->belongsTo(Insured::class);
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Escopos de Consulta
    |--------------------------------------------------------------------------
    */

    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', FinancialStatusEnum::Pending->value);
    }

    public function scopePaid(Builder $query): Builder
    {
        return $query->where('status', FinancialStatusEnum::Paid->value);
    }

    public function scopeOverdue(Builder $query): Builder
    {
        return $query->where('status', FinancialStatusEnum::Overdue->value)
            ->orWhere(function ($q) {
                $q->where('status', FinancialStatusEnum::Pending->value)
                  ->where('due_date', '<', today());
            });
    }

    /*
    |--------------------------------------------------------------------------
    | Acessores e Formatadores
    |--------------------------------------------------------------------------
    */

    public function formattedInstallment(): string
    {
        return "{$this->installment_number}/{$this->total_installments}";
    }

    public function formattedGrossAmount(): string
    {
        return 'R$ ' . number_format((float) $this->gross_amount, 2, ',', '.');
    }

    public function formattedCommissionExpected(): string
    {
        return 'R$ ' . number_format((float) $this->commission_expected, 2, ',', '.');
    }

    public function formattedCommissionReceived(): string
    {
        return $this->commission_received !== null
            ? 'R$ ' . number_format((float) $this->commission_received, 2, ',', '.')
            : '-';
    }

    public function isPaid(): bool
    {
        return $this->status === FinancialStatusEnum::Paid;
    }

    public function isOverdue(): bool
    {
        return $this->status === FinancialStatusEnum::Overdue ||
            ($this->status === FinancialStatusEnum::Pending && $this->due_date && $this->due_date->isPast());
    }
}
