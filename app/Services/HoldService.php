<?php

namespace App\Services;

use App\Events\HoldUnassignedEvent;
use App\Models\Hold\AssignedHold;
use App\Models\Hold\Hold;
use Illuminate\Support\Facades\DB;
use Location\Coordinate;
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
}
