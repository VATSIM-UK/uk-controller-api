<?php

namespace App\Services;

use App\Events\Hold\AircraftEnteredHoldingArea;
use App\Events\Hold\AircraftExitedHoldingArea;
use App\Events\HoldUnassignedEvent;
use App\Models\Hold\AssignedHold;
use App\Models\Hold\Hold;
use App\Models\Navigation\Navaid;
use App\Models\Vatsim\NetworkAircraft;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Location\Distance\Haversine;

class HoldService
{
    /**
     * Returns the current holds in a format
     * that may be converted to a JSON array.
     *
     * @return array
     */
    public function getHolds(): array
    {
        $data = Hold::with('restrictions', 'navaid', 'deemedSeparatedHolds')->get()->toArray();
        foreach ($data as $key => $hold) {
            foreach ($hold['restrictions'] as $restrictionKey => $restriction) {
                $data[$key]['restrictions'][$restrictionKey] =
                    $data[$key]['restrictions'][$restrictionKey]['restriction'];
            }

            foreach ($hold['deemed_separated_holds'] as $separatedKey => $deemedSeparated) {
                $data[$key]['deemed_separated_holds'][$separatedKey] = [
                    'hold_id' => $deemedSeparated['id'],
                    'vsl_insert_distance' => $deemedSeparated['pivot']['vsl_insert_distance'],
                ];
            }

            $data[$key]['fix'] = $data[$key]['navaid']['identifier'];
            unset($data[$key]['navaid_id'], $data[$key]['navaid']);
        }

        return $data;
    }

    public function removeStaleAssignments(): void
    {
        DB::transaction(function () {
            $assignmentsToRemove = AssignedHold::with('aircraft', 'navaid')->get()
                ->filter(function (AssignedHold $hold) {
                    return ($hold->aircraft->groundspeed === 0 && $hold->aircraft->altitude < 1000) ||
                        $hold->aircraft->latLong->getDistance($hold->navaid->coordinate, new Haversine()) > 55000;
                });

            $assignmentsToRemove->each(function (AssignedHold $assignedHold) {
                event(new HoldUnassignedEvent($assignedHold->callsign));
            });

            AssignedHold::whereIn('callsign', $assignmentsToRemove->pluck('callsign'))
                ->delete();
        });
    }

    /**
     * For each network aircraft, check each navaid in turn.
     *
     * Any navaid that the aircraft isn't within close distance of, reject.
     *
     * Those that are left, mark the aircraft as in proximity of, with an "entered" time of whats already there (if
     * exists) or now.
     *
     * Those that are synced represent the navaids that an aircraft could in theory be holding at.
     */
    public function checkAircraftHoldProximity()
    {
        $navaids = Navaid::all();
        $distanceCalculator = new Haversine();

        NetworkAircraft::all()->each(function (NetworkAircraft $aircraft) use ($navaids, $distanceCalculator) {
            $proximityNavaidsBefore = new Collection($aircraft->proximityNavaids->all());

            $changes = $aircraft->proximityNavaids()->sync(
                $navaids->reject(
                    fn (Navaid $navaid) => $navaid->coordinate->getDistance(
                        $aircraft->latLong,
                        $distanceCalculator
                    ) > 18520
                )->mapWithKeys(
                    function (Navaid $navaid) use ($aircraft) {
                        $existingNavaid = $aircraft->proximityNavaids->firstWhere('id', $navaid->id);

                        return [
                            $navaid->id => [
                                'entered_at' => $existingNavaid ? $existingNavaid->pivot->entered_at : Carbon::now()->utc(),
                            ]
                        ];
                    }
                )
            );

            $aircraft->load('proximityNavaids');
            if (count($changes['attached']) > 0) {
                $aircraft->proximityNavaids->each(function (Navaid $navaid) use ($aircraft) {
                    event(new AircraftEnteredHoldingArea($aircraft, $navaid));
                });
            }

            if (count($changes['detached']) > 0) {
                $proximityNavaidsBefore->reject(
                    fn (Navaid $navaid) => $aircraft->proximityNavaids->firstWhere('id', $navaid->id)
                )
                    ->each(function (Navaid $navaid) use ($aircraft) {
                        event(new AircraftExitedHoldingArea($aircraft, $navaid));
                    });
            }
        });
    }
}
