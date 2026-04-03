<?php

namespace App\Allocator\Stand;

use App\Models\Stand\Stand;
use App\Models\Stand\StandReservation;
use App\Models\Vatsim\NetworkAircraft;
use Illuminate\Database\Eloquent\Builder;

/**
 * Matches the network aircraft with a stand reservation based on the pilot CID.
 */
class CidReservedArrivalStandAllocator implements ArrivalStandAllocator
{
    public function allocate(NetworkAircraft $aircraft): ?int
    {
        if ($aircraft->cid === null) {
            return null;
        }

        $reservation = StandReservation::with('stand')
            ->whereHas('stand', function (Builder $standQuery) {
                $standQuery->unoccupied()->unassigned();
            })
            ->where('cid', $aircraft->cid)
            ->active()
            ->first();

        return $reservation
            ? Stand::where('stands.id', $reservation->stand_id)->first()->id
            : null;
    }
}
