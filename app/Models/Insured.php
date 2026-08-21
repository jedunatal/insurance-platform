<?php

namespace App\Models;

use App\Enums\PersonTypeEnum;
use App\Models\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Insured extends Model
{
    use HasFactory;
    use SoftDeletes;
    use BelongsToTenant; 

    protected $fillable = [
        'tenant_id',
        'lead_id',
        'assigned_to',
        'created_by',
        'name',
        'email',
        'phone',
        'document',
        'person_type',
        'birth_date',
        'zip_code',
        'address',
        'number',
        'complement',
        'neighborhood',
        'city',
        'state',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'person_type' => PersonTypeEnum::class,
            'birth_date' => 'date',
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

    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class);
    }

    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function policies(): HasMany
    {
        return $this->hasMany(Policy::class);
    }

    public function installments(): HasMany
    {
        return $this->hasMany(PolicyInstallment::class);
    }

    public function claims(): HasMany
    {
        return $this->hasMany(Claim::class);
    }

    public function quotes(): HasMany
    {
        return $this->hasMany(Quote::class);
    }

    public function renewals(): HasMany
    {
        return $this->hasMany(PolicyRenewal::class);
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
    | Escopos de Consulta (Query Scopes)
    |--------------------------------------------------------------------------
    */

    public function scopeForTenant(Builder $query, int $tenantId): Builder
    {
        return $query->where('tenant_id', $tenantId);
    }

    public function scopeByConsultant(Builder $query, int $userId): Builder
    {
        return $query->where('assigned_to', $userId);
    }

    public function scopeByPersonType(Builder $query, PersonTypeEnum $personType): Builder
    {
        return $query->where('person_type', $personType->value);
    }

    public function scopeByCity(Builder $query, string $city): Builder
    {
        return $query->where('city', 'like', "%{$city}%");
    }

    public function scopeByState(Builder $query, string $state): Builder
    {
        return $query->where('state', strtoupper($state));
    }

    public function scopeSearch(Builder $query, string $term): Builder
    {
        $term = "%{$term}%";

        return $query->where(
            static function (Builder $query) use ($term): void {
                $query
                    ->where('name', 'like', $term)
                    ->orWhere('email', 'like', $term)
                    ->orWhere('phone', 'like', $term)
                    ->orWhere('document', 'like', $term);
            }
        );
    }

    public function scopeCreatedToday(Builder $query): Builder
    {
        return $query->whereDate('created_at', today());
    }

    /**
     * Formata o documento de acordo com o tipo de pessoa.
     */
    public function formattedDocument(): ?string
    {
        if (! $this->document) {
            return null;
        }

        $digits = preg_replace('/\D/', '', $this->document);

        return $this->person_type === PersonTypeEnum::Legal
            ? preg_replace('/^(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})$/', '$1.$2.$3/$4-$5', $digits)
            : preg_replace('/^(\d{3})(\d{3})(\d{3})(\d{2})$/', '$1.$2.$3-$4', $digits);
    }

    public function formattedBirthDate(): ?string
    {
        return $this->birth_date?->format('d/m/Y');
    }

    public function formattedPhone(): ?string
    {
        if (! $this->phone) {
            return null;
        }

        $digits = preg_replace('/\D/', '', $this->phone);

        if (strlen($digits) === 11) {
            return preg_replace('/^(\d{2})(\d{5})(\d{4})$/', '($1) $2-$3', $digits);
        }

        if (strlen($digits) === 10) {
            return preg_replace('/^(\d{2})(\d{4})(\d{4})$/', '($1) $2-$3', $digits);
        }

        return $this->phone;
    }

    public function fullAddress(): ?string
    {
        $parts = array_filter([
            $this->address,
            $this->number ? 'nº ' . $this->number : null,
            $this->complement,
            $this->neighborhood,
            $this->city,
            $this->state,
            $this->zip_code ? 'CEP: ' . $this->zip_code : null,
        ]);

        return $parts !== [] ? implode(', ', $parts) : null;
    }
}
