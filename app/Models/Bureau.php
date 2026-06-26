<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Bureau extends Model
{
    use HasFactory;

    protected $table = 'bureaux';

    protected $fillable = ['nom_bureau', 'chef_bureau_id'];

    public function chefBureau(): BelongsTo
    {
        return $this->belongsTo(User::class, 'chef_bureau_id');
    }

    public function services(): HasMany
    {
        return $this->hasMany(Service::class);
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'bureau_id');
    }
}
