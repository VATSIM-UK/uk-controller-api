<?php

namespace App\Services;

use App\Models\Airfield;
use App\Models\Tma;

class MinStackLevelService
{
    /**
     * @param string $icao
     * @return int|null
     */
    public function getMinStackLevelForAirfield(string $icao) : ?int
    {
        $airfield = Airfield::where('code', $icao)->first();

        if ($airfield === null || $airfield->msl === null) {
            return null;
        }

        return $airfield->msl->msl;
    }

    /**
     * @param string $name
     * @return int|null
     */
    public function getMinStackLevelForTma(string $name) : ?int
    {
        $tma = Tma::where('name', $name)->first();
        if ($tma === null || $tma->msl === null) {
            return null;
        }

        return $tma->msl->msl;
    }

    /**
     * @return array
     */
    public function getAllAirfieldMinStackLevels() : array
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

    /**
     * @return array
     */
    public function getAllTmaMinStackLevels() : array
    {
        $airfields = Tma::all();
        $minStackLevels = [];

        $airfields->each( function (Tma $tma) use (&$minStackLevels) {
            if ($tma->msl === null) {
                return;
            }

            $minStackLevels[$tma->name] = $tma->msl->msl;
        });

        return $minStackLevels;
    }
}
