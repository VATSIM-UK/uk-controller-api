<?php

namespace App\Allocator\Stand;

use App\Models\Stand\Stand;
use App\Models\Stand\StandReservation;
use App\Models\Vatsim\NetworkAircraft;
use Illuminate\Database\Eloquent\Builder;

/**
 * Matches the network aircraft with a stand that is allocated to an aircraft of the same callsign
 * with the same flightplan (origin/dest).
 */
class CallsignFlightplanReservedArrivalStandAllocator extends AbstractArrivalStandAllocator
{
    protected function getOrderedStandsQuery(Builder $stands, NetworkAircraft $aircraft): ?Builder
    {
        $reservation = StandReservation::with('stand')
            ->whereHas('stand', function (Builder $standQuery) {
                $standQuery->unoccupied()->unassigned();
            })
            ->where('callsign', $aircraft->callsign)
            ->where('origin', $aircraft->planned_depairport)
            ->where('destination', $aircraft->planned_destairport)
            ->active()
            ->first();

        return $reservation
            ? Stand::where('stands.id', $reservation->stand_id)->select('stands.*')
            : null;
    }
}
