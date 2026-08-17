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
    private function sizeAppropriateAvailableStandsAtAirfield(NetworkAircraft $aircraft): Builder
    {
        $isForDeparture = $aircraft->isForDeparture ?? false;

        $query = Stand::whereHas('airfield', function (Builder $query) use ($aircraft, $isForDeparture) {
            $query->where('code', $isForDeparture ? $aircraft->planned_depairport : $aircraft->planned_destairport);
        })
            ->sizeAppropriate($aircraft->aircraft);

        return ($isForDeparture ? $query->available() : $query->availableForArrival())
            ->select('stands.*');
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
}
