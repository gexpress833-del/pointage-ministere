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

    public const CLE_HEURE_REFERENCE_DEPART = 'heure_reference_depart';

    public const CLE_HEURE_OUVERTURE_SESSION = 'heure_ouverture_session';

    public const CLE_HEURE_FIN_ARRIVEE = 'heure_fin_arrivee';

    public const CLE_HEURE_DEBUT_DEPART = 'heure_debut_depart';

    public const CLE_HEURE_FIN_DEPART_NORMAL = 'heure_fin_depart_normal';

    public const CLE_HEURE_FERMETURE_SESSION = 'heure_fermeture_session';

    public const CLE_SEUIL_RECONNAISSANCE = 'seuil_reconnaissance_faciale';

    public const CLE_SESSION_AUTO_OPEN = 'session_auto_open';

    public const CLE_SESSION_AUTO_CLOSE = 'session_auto_close';

    public const CLE_SESSION_ALLOW_REOPEN = 'session_allow_reopen';

    public const CLE_SESSION_ALLOW_RESET_PRESENCES = 'session_allow_reset_presences';

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
        return static::getTimeValue(self::CLE_HEURE_LIMITE_RETARD, '08:59');
    }

    public static function heureReferenceDepart(): string
    {
        return static::getTimeValue(self::CLE_HEURE_REFERENCE_DEPART, '16:59');
    }

    public static function heureOuvertureSession(): string
    {
        return static::getTimeValue(self::CLE_HEURE_OUVERTURE_SESSION, '07:59');
    }

    public static function heureFinArrivee(): string
    {
        return static::getTimeValue(self::CLE_HEURE_FIN_ARRIVEE, '11:59');
    }

    public static function heureDebutDepart(): string
    {
        return static::getTimeValue(self::CLE_HEURE_DEBUT_DEPART, '15:59');
    }

    public static function heureFinDepartNormal(): string
    {
        return static::getTimeValue(self::CLE_HEURE_FIN_DEPART_NORMAL, '16:59');
    }

    public static function heureFermetureSession(): string
    {
        return static::getTimeValue(self::CLE_HEURE_FERMETURE_SESSION, '23:59');
    }

    public static function sessionAutoOpen(): bool
    {
        return (bool) static::getValue(self::CLE_SESSION_AUTO_OPEN, true);
    }

    public static function sessionAutoClose(): bool
    {
        return (bool) static::getValue(self::CLE_SESSION_AUTO_CLOSE, true);
    }

    public static function sessionAllowReopen(): bool
    {
        return (bool) static::getValue(self::CLE_SESSION_ALLOW_REOPEN, true);
    }

    public static function sessionAllowResetPresences(): bool
    {
        return (bool) static::getValue(self::CLE_SESSION_ALLOW_RESET_PRESENCES, true);
    }

    private static function getTimeValue(string $key, string $default): string
    {
        $value = (string) static::getValue($key, $default);

        if (! preg_match('/^(?:[01]\d|2[0-3]):[0-5]\d(?::[0-5]\d)?$/', $value)) {
            return $default;
        }

        return $value;
    }

    public static function clearCache(): void
    {
        $cles = static::pluck('cle');
        foreach ($cles as $cle) {
            Cache::forget('parametre_'.$cle);
        }
    }
}
