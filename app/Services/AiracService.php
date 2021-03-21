<?php

namespace App\Services;

use Carbon\Carbon;

class AiracService
{
    public static function getBaseAiracDate(): Carbon
    {
        return Carbon::parse('2021-01-28 00:00:00');
    }

    private static function getDaysSinceBaseAiracDate(Carbon $date): int
    {
        return self::getBaseAiracDate()->diffInDays($date);
    }

    private static function getPreviousAiracDayFromDate(Carbon $date): Carbon
    {
        return $date->subDays(self::getDaysSinceBaseAiracDate($date) % 28)->startOfDay();
    }

    public static function getPreviousAiracDay(): Carbon
    {
        return self::getPreviousAiracDayFromDate(Carbon::now());
    }
}
