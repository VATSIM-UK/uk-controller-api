<?php

namespace App\Allocator\Stand;

use App\Models\Stand\Stand;
use App\Models\Stand\StandReservation;
use App\Models\Vatsim\NetworkAircraft;
use Illuminate\Database\Eloquent\Builder;

/**
 * Matches the network aircraft with a stand reservation based on the pilots CID.
 */
class CidReservedArrivalStandAllocator implements ArrivalStandAllocator
{
    public function allocate(NetworkAircraft $aircraft): ?int
    {
        $reservation = StandReservation::with('stand')
            ->whereHas('stand', function (Builder $standQuery) {
                $standQuery->unoccupied()->unassigned();
            })
            ->where('cid', $aircraft->cid)
            ->where('destination', $aircraft->planned_destairport)
            ->active()
            ->first();

        return $reservation
            ? Stand::where('stands.id', $reservation->stand_id)->first()->id
            : null;
    }
}
