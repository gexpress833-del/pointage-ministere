<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Service extends Model
{
    use HasFactory;

    protected $fillable = ['nom_service', 'bureau_id'];

    public function bureau(): BelongsTo
    {
        return $this->belongsTo(Bureau::class);
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'service_id');
    }
}
