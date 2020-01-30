<?php

namespace App\Services;

use App\Models\Airfield\Airfield;

class AirfieldService
{
    /**
     * @return array
     */
    public function getAllAirfieldsWithRelations() : array
    {
        $airfields = [];
        Airfield::all()->each(function (Airfield $airfield) use (&$airfields) {
            $prenotePairings = $airfield->prenotePairings()->select(['destination_airfield_id', 'prenote_id'])->get();
            $prenoteArray = [];

            $prenotePairings->each(function (Airfield $airfield) use (&$prenoteArray) {
                $prenoteArray[$airfield->destination_airfield_id][] = $airfield->prenote_id;
            });

            $airfields[] = array_merge(
                $airfield->toArray(),
                [
                    'controllers' =>
                    $airfield->controllers()->orderBy('order')->pluck('controller_position_id')->toArray(),
                    'pairing-prenotes' => $prenoteArray
                ]
            );
        });

        return $airfields;
    }
}
