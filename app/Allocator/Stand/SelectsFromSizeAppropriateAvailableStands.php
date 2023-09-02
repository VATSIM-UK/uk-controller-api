<?php

namespace App\Allocator\Stand;

use App\Models\Stand\Stand;
use App\Models\Vatsim\NetworkAircraft;
use Illuminate\Database\Eloquent\Builder;

trait SelectsFromSizeAppropriateAvailableStands {
    /*
     * Base query for stands at the arrival airfield, which are of a suitable
     * size (or max size if no type) for the aircraft and not occupied.
     */
    private function sizeAppropriateAvailableStandsAtAirfield(NetworkAircraft $aircraft): Builder
    {
        return Stand::whereHas('airfield', function (Builder $query) use ($aircraft)
        {
            $query->where('code', $aircraft->planned_destairport);
        })
            ->sizeAppropriate($aircraft->aircraft)
            ->available()
            ->select('stands.*');
    }
}
