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
    // Holding area radius in meters. Equivalent to 12nm.
    private const HOLDING_AREA_RADIUS = 22224;

    // Hold entry radius in meters. Equivalent to 3nm.
    private const HOLD_ENTRY_RADIUS = 5556;

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
        $this->removeLandedAircraftFromProximityHolds();
        $this->setProximityHoldsForAirborneAircraft();
    }

    private function setProximityHoldsForAirborneAircraft(): void
    {
        $navaids = Navaid::all();
        $distanceCalculator = new Haversine();

        $this->getAircraftEligibleForProximityHolding()->each(
            function (NetworkAircraft $aircraft) use ($navaids, $distanceCalculator) {
                $proximityNavaidsBefore = new Collection($aircraft->proximityNavaids->all());

                /**
                 * The aircraft is now holding at any navaid that it's within 3nm of, so attach any that we aren't
                 * already holding at.
                 */
                $toAttach = $navaids->reject(
                    fn (Navaid $navaid) => $navaid->coordinate->getDistance(
                        $aircraft->latLong,
                        $distanceCalculator
                    ) > self::HOLD_ENTRY_RADIUS
                )->reject(
                    fn (Navaid $navaid) => $proximityNavaidsBefore->contains(
                        fn (Navaid $proximityNavaid) => $proximityNavaid->id === $navaid->id
                    )
                );

                if ($toAttach->isNotEmpty()) {
                    $aircraft->proximityNavaids()->attach(
                        $toAttach->mapWithKeys(
                            fn (Navaid $navaid) => [
                                $navaid->id => [
                                    'entered_at' => Carbon::now()->utc(),
                                ]
                            ]
                        )
                    );

                    $aircraft->proximityNavaids()->whereIn('navaids.id', $toAttach->pluck('id'))
                        ->each(function (Navaid $navaid) use ($aircraft) {
                            event(new AircraftEnteredHoldingArea($aircraft, $navaid));
                        });
                }

                /**
                 * Any navaid that we're a long way from, assume that the hold has been left. Detach these holds.
                 */
                $toDetach = $aircraft->proximityNavaids->filter(fn (Navaid $navaid) => $navaid->coordinate->getDistance(
                    $aircraft->latLong,
                    $distanceCalculator
                ) > self::HOLDING_AREA_RADIUS);

                if ($toDetach->isNotEmpty()) {
                    $aircraft->proximityNavaids()->detach($toDetach->pluck('id'));
                    $toDetach->each(function (Navaid $navaid) use ($aircraft) {
                        event(new AircraftExitedHoldingArea($aircraft, $navaid));
                    });
                }
            }
        );
    }

    private function getAircraftEligibleForProximityHolding(): Collection
    {
        return NetworkAircraft::where('groundspeed', '>=', 50)
            ->where('altitude', '>=', 1000)
            ->get();
    }

    private function removeLandedAircraftFromProximityHolds(): void
    {
        NetworkAircraft::whereHas('proximityNavaids')
            ->where('groundspeed', '<', 50)
            ->where('altitude', '<', 1000)
            ->get()
            ->each(function (NetworkAircraft $aircraft) {
                $aircraft->proximityNavaids->each(function (Navaid $navaid) use ($aircraft) {
                    event(new AircraftExitedHoldingArea($aircraft, $navaid));
                });
                $aircraft->proximityNavaids()->sync([]);
            });
    }
}
