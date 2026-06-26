<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SessionPresence extends Model
{
    use HasFactory;

    protected $table = 'sessions_presences';

    protected $fillable = ['date', 'statut', 'opened_by', 'closed_by', 'closed_at'];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'closed_at' => 'datetime',
        ];
    }

    public const STATUT_OUVERTE = 'ouverte';

    public const STATUT_FERMEE = 'fermee';

    public function openedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'opened_by');
    }

    public function closedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'closed_by');
    }

    public function presences(): HasMany
    {
        return $this->hasMany(Presence::class, 'session_id');
    }

    public function isOuverte(): bool
    {
        return $this->statut === self::STATUT_OUVERTE;
    }
}
