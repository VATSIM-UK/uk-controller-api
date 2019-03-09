<?php

namespace App\Services;

use App\Models\Airfield;
use App\Models\MinStack\MslAirfield;
use App\Models\MinStack\MslTma;
use App\Models\Tma;

class MinStackLevelService
{
    public function getMinStackLevelForAirfield(string $icao) : ?MslAirfield
    {
        $airfield = Airfield::where('code', $icao)->first();

        if ($airfield === null || $airfield->msl === null) {
            return null;
        }

        return $airfield->msl;
    }

    public function getMinStackLevelForTma(string $name) : ?MslTma
    {
        $tma = Tma::where('name', $name)->first();
        if ($tma === null || $tma->msl === null) {
            return null;
        }

        return $tma->msl;
    }
}
