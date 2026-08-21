<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory;
    use HasRoles;
    use Notifiable;

    protected $with = [];

    /**
     * Auto-purge de caches do Spatie Permission ao recarregar o usuário.
     */
    protected static function booted(): void
    {
        //
    }

    protected $fillable = [
        'tenant_id',
        'name',
        'email',
        'phone',
        'password',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
            'is_active'         => 'boolean',
        ];
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function assignedLeads(): HasMany
    {
        return $this->hasMany(Lead::class, 'assigned_to');
    }

    public function createdLeads(): HasMany
    {
        return $this->hasMany(Lead::class, 'created_by');
    }

    /**
     * Retorna o título legível do cargo/role principal do usuário.
     */
    public function roleTitle(): string
    {
        if ($this->hasRole('super-admin') || $this->hasRole('admin')) {
            return 'Administrador Geral';
        }

        if ($this->hasRole('broker')) {
            return 'Corretor Gestor';
        }

        if ($this->hasRole('assistant') || $this->hasRole('consultant')) {
            return 'Assistente / Consultor';
        }

        return 'Membro da Equipe';
    }

    public function canManageTeam(): bool
    {
        return $this->hasRole(['super-admin', 'admin', 'broker']);
    }

    public function canManageFinancial(): bool
    {
        return $this->hasRole(['super-admin', 'admin', 'broker']);
    }
}