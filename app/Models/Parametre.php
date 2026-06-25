<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Parametre extends Model
{
    protected $fillable = ['cle', 'valeur', 'type', 'description'];

    protected static function booted(): void
    {
        static::saved(function (self $parametre): void {
            Cache::forget('parametre_'.$parametre->cle);
        });

        static::deleted(function (self $parametre): void {
            Cache::forget('parametre_'.$parametre->cle);
        });
    }

    public const CLE_HEURE_LIMITE_RETARD = 'heure_limite_retard';

    /** Heure de référence pour le départ (fin de journée attendue), HH:MM — indicatif / rapports. */
    public const CLE_HEURE_REFERENCE_DEPART = 'heure_reference_depart';

    public const CLE_SEUIL_RECONNAISSANCE = 'seuil_reconnaissance_faciale';

    /**
     * Récupère la valeur d'un paramètre (avec cache).
     */
    public static function getValue(string $cle, mixed $default = null): mixed
    {
        $key = 'parametre_'.$cle;
        $param = Cache::remember($key, 3600, function () use ($cle) {
            return static::where('cle', $cle)->first();
        });
        if (! $param) {
            return $default;
        }

        return static::castValue($param->valeur, $param->type);
    }

    protected static function castValue(?string $valeur, string $type): mixed
    {
        if ($valeur === null) {
            return null;
        }

        return match ($type) {
            'integer' => (int) $valeur,
            'boolean' => filter_var($valeur, FILTER_VALIDATE_BOOLEAN),
            'time' => $valeur, // "08:00" ou "08:00:00"
            default => $valeur,
        };
    }

    /**
     * Heure limite pour considérer un retard (format "H:i" ou "H:i:s").
     */
    public static function heureLimiteRetard(): string
    {
        return (string) static::getValue(self::CLE_HEURE_LIMITE_RETARD, '08:00');
    }

    /**
     * Heure de référence de fin de journée / départ attendu (format "H:i" ou "H:i:s").
     */
    public static function heureReferenceDepart(): string
    {
        return (string) static::getValue(self::CLE_HEURE_REFERENCE_DEPART, '17:00');
    }

    public static function clearCache(): void
    {
        $cles = static::pluck('cle');
        foreach ($cles as $cle) {
            Cache::forget('parametre_'.$cle);
        }
    }
}
