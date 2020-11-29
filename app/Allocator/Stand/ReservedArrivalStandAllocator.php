<?php

namespace App\Allocator\Stand;

use App\Models\Stand\StandReservation;
use App\Models\Vatsim\NetworkAircraft;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class ReservedArrivalStandAllocator extends AbstractArrivalStandAllocator
{
    protected function getPossibleStands(NetworkAircraft $aircraft): Collection
    {
        $reservation = StandReservation::with('stand')
            ->whereHas('stand', function (Builder $standQuery) {
                $standQuery->unoccupied()->unassigned();
            })
            ->where('callsign', $aircraft->callsign)
            ->active()
            ->first();

        return $reservation ? new Collection([$reservation->stand]) : new Collection();
    }
}
