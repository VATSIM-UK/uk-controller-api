<?php

namespace App\Services;

use Carbon\Carbon;
use Carbon\CarbonInterface;
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

    private static function getNextAiracDayFromDate(Carbon $date): Carbon
    {
        return $date->addDays(
            self::AIRAC_INTERVAL - (self::getDaysSinceBaseAiracDate($date) % self::AIRAC_INTERVAL)
        )->startOfDay();
    }

    public static function getPreviousAiracDay(): Carbon
    {
        return self::getPreviousAiracDayFromDate(Carbon::now());
    }

    public static function getCurrentAirac(): string
    {
        $firstAiracDayOfYear = self::getNextAiracDayFromDate(Carbon::now()->startOfYear());

        return Carbon::now() < $firstAiracDayOfYear
            ? self::formatAirac(Carbon::now()->subYear(), 13)
            : self::formatAirac(
                Carbon::now(),
                ($firstAiracDayOfYear->diffInDays(Carbon::now()) / self::AIRAC_INTERVAL) + 1
            );
    }

    private static function formatAirac(CarbonInterface $date, int $number): string
    {
        return sprintf(
            '%s%s',
            $date->format('y'),
            Str::padLeft(
                $number,
                2,
                '0'
            )
        );
    }
}
