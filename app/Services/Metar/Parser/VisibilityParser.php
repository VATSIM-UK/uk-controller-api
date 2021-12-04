<?php

namespace App\Services\Metar\Parser;

use App\Models\Airfield\Airfield;
use Illuminate\Support\Collection;

class VisibilityParser implements MetarParser
{
    const VISIBLITY_PATTERN = '/^\\d{4}$/';

    public function parse(Airfield $airfield, Collection $metarTokens): Collection
    {
        return tap(
            collect(),
            function (Collection $parsedData) use ($metarTokens) {
                $metarTokens->each(function (string $token) use ($parsedData) {
                    $matches = [];
                    if (preg_match(self::VISIBLITY_PATTERN, $token, $matches) !== 1) {
                        return true;
                    }

                    $parsedData->offsetSet('visibility', (int)$matches[0]);
                    return false;
                });
            }
        );
    }
}
