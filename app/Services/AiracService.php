<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Str;

class AiracService
{
    private const AIRAC_INTERVAL = 28;

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
        return $date->subDays(self::getDaysSinceBaseAiracDate($date) % self::AIRAC_INTERVAL)->startOfDay();
    }

    public static function getPreviousAiracDay(): Carbon
    {
        return self::getPreviousAiracDayFromDate(Carbon::now());
    }

    public static function getCurrentAirac(): string
    {
        $previousAiracDay = self::getPreviousAiracDay()->toImmutable();

        return sprintf(
            '%s%s',
            $previousAiracDay->format('y'),
            Str::padLeft(
                (int)($previousAiracDay->startOfYear()->diffInDays(Carbon::now()) / self::AIRAC_INTERVAL),
                '2',
                '0'
            )
        );
    }
}
