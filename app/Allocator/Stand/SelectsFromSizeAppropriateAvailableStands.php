<?php

namespace App\Allocator\Stand;

use App\Models\Stand\Stand;
use App\Models\Vatsim\NetworkAircraft;
use Illuminate\Database\Eloquent\Builder;

trait SelectsFromSizeAppropriateAvailableStands
{
    /*
     * Base query for stands at the airfield being allocated to, which are of a suitable
     * size (or max size if no type) for the aircraft and not occupied.
     */
    private function sizeAppropriateAvailableStandsAtAirfield(
        NetworkAircraft $aircraft,
        StandAllocationType $type = StandAllocationType::Arrival
    ): Builder {
        $query = Stand::whereHas('airfield', function (Builder $query) use ($aircraft, $type) {
            $query->where('code', $this->allocationAirfield($aircraft, $type));
        })
            ->sizeAppropriate($aircraft->aircraft);

        return ($type === StandAllocationType::Arrival
            ? $query->availableForArrival()
            : $query->available()
        )->select('stands.*');
    }

    private function sizeAppropriateAvailableStandsAtAirfieldForRanking(NetworkAircraft $aircraft): Builder
    {
        return Stand::whereHas('airfield', function (Builder $query) use ($aircraft) {
            $query->where('code', $aircraft->planned_destairport);
        })
            ->sizeAppropriate($aircraft->aircraft)
            ->allocationOpen()
            ->select('stands.*');
    }

    private function allocationAirfield(NetworkAircraft $aircraft, StandAllocationType $type): ?string
    {
        return $type === StandAllocationType::Arrival
            ? $aircraft->planned_destairport
            : $aircraft->planned_depairport;
    }
}
