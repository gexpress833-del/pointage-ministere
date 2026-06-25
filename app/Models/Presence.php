<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Presence extends Model
{
    use HasFactory;

    protected $fillable = ['session_id', 'user_id', 'heure_arrivee', 'heure_depart', 'photo_capture', 'statut'];

    protected function casts(): array
    {
        return [];
    }

    public const STATUT_PRESENT = 'present';

    public const STATUT_RETARD = 'retard';

    public const STATUT_ABSENT = 'absent';

    public function session(): BelongsTo
    {
        return $this->belongsTo(SessionPresence::class, 'session_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
