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

    /**
     * @return array
     */
    public function getAllSids() : array
    {
        $sids = [];
        Sid::all()->each(function (Sid $sid) use (&$sids) {
            $sids[] = array_merge(
                $sid->toArray(),
                [
                    'prenotes' => $sid->prenotes()->pluck('prenote_id')->toArray(),
                ]
            );
        });

        return $sids;
    }

    /**
     * @param int $id
     * @return array
     */
    public function getSid(int $id) : ?array
    {
        $sid = Sid::find($id);
        return $sid ? $sid->toArray() : null;
    }

    /**
     * @param int $sidId
     * @return bool
     */
    public function deleteSid(int $sidId) : bool
    {
        $sid = Sid::find($sidId);
        return $sid ? $sid->delete() : false;
    }

    /**
     * @param int $airfieldId
     * @param string $identifier
     * @param int $initialAltitude
     */
    public function createSid(int $runwayId, string $identifier, int $initialAltitude) : void
    {
        Sid::create(
            [
                'runway_id' => $runwayId,
                'identifier' => $identifier,
                'initial_altitude' => $initialAltitude,
                'created_at' => Carbon::now(),
            ]
        );
    }

    /**
     * @param int $id
     * @param int $airfieldId
     * @param string $identifier
     * @param int $initialAltitude
     */
    public function updateSid(int $id, int $runwayId, string $identifier, int $initialAltitude) : void
    {
        $sid = Sid::find($id);
        $sid->runway_id = $runwayId;
        $sid->identifier = $identifier;
        $sid->initial_altitude = $initialAltitude;
        $sid->save();
    }
}
