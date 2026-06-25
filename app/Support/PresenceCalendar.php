<?php

namespace App\Support;

use Carbon\Carbon;

class PresenceCalendar
{
    /**
     * Libellé du jour férié ou null.
     */
    public static function libelleJourFerie(Carbon $date): ?string
    {
        $ymd = $date->format('Y-m-d');
        $extras = config('presence_calendar.extra_holidays', []);
        if (isset($extras[$ymd])) {
            return $extras[$ymd];
        }

        $key = $date->format('m-d');
        $recurring = config('presence_calendar.recurring_holidays', []);

        return $recurring[$key] ?? null;
    }

    public static function estJourFerie(Carbon $date): bool
    {
        return self::libelleJourFerie($date) !== null;
    }

    /**
     * Abréviation du jour de la semaine (lun. … dim.), locale fr.
     */
    public static function abregeJourSemaine(Carbon $date): string
    {
        return mb_substr($date->copy()->locale('fr')->translatedFormat('l'), 0, 3).'.';
    }

    /**
     * True si la date (jour civil) est strictement après « aujourd’hui » (fuseau application).
     */
    public static function estJourFutur(int $year, int $month, int $day): bool
    {
        $d = Carbon::createFromDate($year, $month, $day)->startOfDay();

        return $d->gt(Carbon::today());
    }
}
