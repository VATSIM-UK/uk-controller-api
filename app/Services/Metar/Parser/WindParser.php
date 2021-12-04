<?php

namespace App\Services\Metar\Parser;

use App\Models\Airfield\Airfield;
use Illuminate\Support\Collection;

class WindParser implements MetarParser
{
    const WIND_REGEX = '/^(\\d{3})(\\d{2,3})(G(\\d{2,3}))?KT$/';

    public function parse(Airfield $airfield, Collection $metarTokens): Collection
    {
        return tap(
            collect(),
            function (Collection $parsedData) use ($metarTokens) {
                $metarTokens->each(function (string $token) use ($parsedData) {
                    $tokenMatches = [];

                    if (preg_match(self::WIND_REGEX, $token, $tokenMatches) === 1) {
                        $parsedData->offsetSet('wind_direction', $tokenMatches[1]);
                        $parsedData->offsetSet('wind_speed', $tokenMatches[2]);
                        $parsedData->offsetSet('wind_gust', $tokenMatches[4] ?? null);
                        return false;
                    }

                    return true;
                });
            }
        );
    }
}
