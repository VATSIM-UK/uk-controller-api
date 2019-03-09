<?php

namespace App\Services;

use App\Models\Airfield;
use App\Models\Tma;

class MinStackLevelService
{
    public function getMinStackLevelForAirfield(string $icao) : ?int
    {
        $airfield = Airfield::where('code', $icao)->first();

        if ($airfield === null || $airfield->msl === null) {
            return null;
        }

        return $airfield->msl->msl;
    }

    public function getMinStackLevelForTma(string $name) : ?int
    {
        $tma = Tma::where('name', $name)->first();
        if ($tma === null || $tma->msl === null) {
            return null;
        }

        return $tma->msl->msl;
    }

    public function getAllAirfieldMinStackLevels()
    {
        $airfields = Airfield::all();
        $minStackLevels = [];

        $airfields->each( function (Airfield $airfield) use (&$minStackLevels) {
            if ($airfield->msl === null) {
                return;
            }

            $minStackLevels[$airfield->code] = $airfield->msl->msl;
        });

        return $minStackLevels;
    }
}
