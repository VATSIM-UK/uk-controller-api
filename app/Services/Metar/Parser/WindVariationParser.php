<?php

namespace App\Services\Metar\Parser;

use App\Models\Airfield\Airfield;
use Illuminate\Support\Collection;

class WindVariationParser implements MetarParser
{
    const WIND_VARIATION_REGEX = '/^(\\d{3})V(\\d{3})$/';

    public function parse(Airfield $airfield, Collection $metarTokens): Collection
    {
        return tap(
            collect(),
            function (Collection $parsedData) use ($metarTokens) {
                $metarTokens->each(function (string $token) use ($parsedData) {
                    $tokenMatches = [];

                    if (preg_match(self::WIND_VARIATION_REGEX, $token, $tokenMatches) === 1) {
                        $parsedData->offsetSet('wind_variation', $tokenMatches[0]);
                        return false;
                    }

                    return true;
                });
            }
        );
    }
}
