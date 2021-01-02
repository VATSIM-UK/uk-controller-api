<?php

namespace App\Services;

use App\Models\Airfield\Airfield;
use App\Models\Controller\Prenote;
use App\Models\Sid;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;

class SidService
{
    /**
     * @return array
     */
    public function getInitialAltitudeDependency() : array
    {
        $sidGroups = Sid::all()->groupBy('airfield_id');

        $altitudes = [];
        $sidGroups->each(function (Collection $airfieldGroup) use (&$altitudes) {
            $airfieldModel = Airfield::find($airfieldGroup->first()->airfield_id);

            $airfieldGroup->each(function (Sid $sid) use (&$altitudes, $airfieldModel) {
                $altitudes[$airfieldModel->code][$sid->identifier] = $sid->initial_altitude;
            });
        });

        return $altitudes;
    }

    public function getSidsDependency()
    {
        return Sid::with('airfield', 'prenotes')->get()->map(function (Sid $sid) {
            return [
                'id' => $sid->id,
                'airfield' => $sid->airfield->code,
                'identifier' => $sid->identifier,
                'departure_interval_group' => $sid->sid_departure_interval_group_id,
                'initial_altitude' => $sid->initial_altitude,
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
    public function createSid(int $airfieldId, string $identifier, int $initialAltitude) : void
    {
        Sid::create(
            [
                'airfield_id' => $airfieldId,
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
    public function updateSid(int $id, int $airfieldId, string $identifier, int $initialAltitude) : void
    {
        $sid = Sid::find($id);
        $sid->airfield_id = $airfieldId;
        $sid->identifier = $identifier;
        $sid->initial_altitude = $initialAltitude;
        $sid->save();
    }
}
