<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuoteOption extends Model
{
    use HasFactory;

    protected $table = 'quote_options';

    protected $fillable = [
        'quote_id',
        'insurer',
        'product_name',
        'net_premium',
        'iof_amount',
        'total_premium',
        'deductible_type',
        'deductible_amount',
        'car_rental',
        'glass_coverage',
        'third_party_materials',
        'third_party_corporal',
        'app_coverage',
        'payment_conditions',
        'is_recommended',
        'is_accepted',
        'highlights',
        'notes',
    ];

    protected $casts = [
        'net_premium'           => 'decimal:2',
        'iof_amount'            => 'decimal:2',
        'total_premium'         => 'decimal:2',
        'deductible_amount'     => 'decimal:2',
        'third_party_materials' => 'decimal:2',
        'third_party_corporal'  => 'decimal:2',
        'app_coverage'          => 'decimal:2',
        'is_recommended'        => 'boolean',
        'is_accepted'           => 'boolean',
    ];

    public function quote(): BelongsTo
    {
        return $this->belongsTo(Quote::class);
    }

    public function formattedTotalPremium(): string
    {
        return 'R$ ' . number_format((float) $this->total_premium, 2, ',', '.');
    }

    public function formattedDeductibleAmount(): string
    {
        return 'R$ ' . number_format((float) $this->deductible_amount, 2, ',', '.');
    }
}
