<?php

namespace App\Services\Metar\Parser;

use App\Models\Airfield\Airfield;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class ObservationTimeParser implements MetarParser
{
    const TIME_PATTERN = '/^(\\d{2})(\\d{4})Z$/';

    public function parse(Airfield $airfield, Collection $metarTokens): Collection
    {
        return tap(
            collect(),
            function (Collection $parsedData) use ($metarTokens) {
                $metarTokens->each(function (string $token) use ($parsedData) {
                    return !$this->parseTime($token, $parsedData);
                });
            }
        );
    }

    private function parseTime(string $metarToken, Collection $parsedData): bool
    {
        $matches = [];
        if (preg_match(self::TIME_PATTERN, $metarToken, $matches) !== 1) {
            return false;
        }

        $parsedData->offsetSet(
            'observation_time',
            $this->carbonFromMetarTime($matches[1], $matches[2])
        );
        return true;
    }

    private function carbonFromMetarTime(string $dayOfMonth, string $time): Carbon
    {
        return Carbon::create(null, null, (int)$dayOfMonth, (int) Str::substr($time, 0, 2), (int) Str::substr($time, 2));
    }
}
