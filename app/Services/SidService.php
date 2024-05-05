<?php

namespace App\Services;

use App\Models\Controller\Prenote;
use App\Models\Sid;
use Illuminate\Support\Carbon;

class SidService
{
    public function getSidsDependency()
    {
        return Sid::with('runway', 'runway.airfield', 'prenotes')->get()->map(function (Sid $sid) {
            return [
                'id' => $sid->id,
                'airfield' => $sid->runway->airfield->code,
                'runway_id' => $sid->runway_id,
                'identifier' => $sid->identifier,
                'departure_interval_group' => $sid->sid_departure_interval_group_id,
                'initial_altitude' => $sid->initial_altitude,
                'initial_heading' => $sid->initial_heading,
                'handoff' => $sid->handoff_id,
                'prenotes' => $sid->prenotes->map(function (Prenote $prenote) {
                    return $prenote->id;
                })->toArray(),
            ];
        })->toArray();
    }
}
