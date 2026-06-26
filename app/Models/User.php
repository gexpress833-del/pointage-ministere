<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements FilamentUser
{
    use HasFactory, Notifiable;

    public const ROLE_ADMIN = 'administrateur';

    public const ROLE_COORDINATEUR = 'coordinateur';

    public const ROLE_CHEF_BUREAU = 'chef_bureau';

    public const ROLE_AGENT = 'agent';

    protected $fillable = [
        'name',
        'nom',
        'matricule',
        'email',
        'telephone',
        'adresse_residence',
        'photo_reference',
        'bureau_id',
        'service_id',
        'role',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function bureau(): BelongsTo
    {
        return $this->belongsTo(Bureau::class);
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function presences(): HasMany
    {
        return $this->hasMany(Presence::class);
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return in_array($this->role, [
            self::ROLE_ADMIN,
            self::ROLE_COORDINATEUR,
            self::ROLE_CHEF_BUREAU,
            self::ROLE_AGENT,
        ], true);
    }

    public function isAdministrateur(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    public function isCoordinateur(): bool
    {
        return $this->role === self::ROLE_COORDINATEUR;
    }

    public function isChefBureau(): bool
    {
        return $this->role === self::ROLE_CHEF_BUREAU;
    }

    public function isAgent(): bool
    {
        return $this->role === self::ROLE_AGENT;
    }

    public function getDisplayName(): string
    {
        return $this->nom ?? $this->name ?? $this->email;
    }
}
