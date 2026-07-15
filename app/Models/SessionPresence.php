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

    protected $fillable = ['date', 'statut', 'opened_by', 'opened_at', 'closed_by', 'closed_at'];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'opened_at' => 'datetime',
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

    public function isFermee(): bool
    {
        return $this->statut === self::STATUT_FERMEE;
    }

    public function open(int $userId): void
    {
        $this->update([
            'statut' => self::STATUT_OUVERTE,
            'opened_by' => $userId,
            'opened_at' => now(),
            'closed_by' => null,
            'closed_at' => null,
        ]);
    }

    public function close(int $userId): void
    {
        $this->update([
            'statut' => self::STATUT_FERMEE,
            'closed_by' => $userId,
            'closed_at' => now(),
        ]);
    }

    public function reopen(int $userId): void
    {
        $this->update([
            'statut' => self::STATUT_OUVERTE,
            'opened_by' => $userId,
            'opened_at' => now(),
            'closed_by' => null,
            'closed_at' => null,
        ]);
    }
}
