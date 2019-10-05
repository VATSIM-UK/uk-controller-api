<?php

namespace App\Services;

use App\Models\Airfield;

class AirfieldService
{
    /**
     * @return array
     */
    public function getAllAirfieldsWithTopDown() : array
    {
        $airfields = Airfield::all();

        $airfieldArray = [];
        $airfields->each(function (Airfield $airfield) use (&$airfieldArray) {
            $airfieldArray[] = array_merge(
                $airfield->toArray(),
                [
                    'controllers' =>
                    $airfield->controllers()->orderBy('order')->pluck('controller_position_id')->toArray()
                ]
            );
        });

        return $airfieldArray;
    }
}
