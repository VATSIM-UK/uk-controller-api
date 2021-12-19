<?php


namespace App\Services\Metar\Parser;

use App\Models\Airfield\Airfield;
use Illuminate\Support\Collection;

/**
 * Interface for classes that can parse raw METAR data ready
 * for insertion into the `parsed` JSON for each METAR.
 */
interface MetarParser
{
    /**
     * Parse the METAR from its tokens and add any data to the parsed data
     */
    public function parse(Airfield $airfield, Collection $metarTokens): Collection;
}
