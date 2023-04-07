<?php

namespace App\Allocator\Stand;

use App\Allocator\UsesDestinationStrings;
use App\Models\Vatsim\NetworkAircraft;
use Illuminate\Database\Eloquent\Builder;

class OriginAirfieldStandAllocator extends AbstractArrivalStandAllocator
{
    use UsesDestinationStrings;

    protected function getOrderedStandsQuery(Builder $stands, NetworkAircraft $aircraft): ? Builder
    {
        return $stands
            ->whereIn('origin_slug', $this->getDestinationStrings($aircraft))
            ->orderByRaw('origin_slug IS NOT NULL')
            ->orderByRaw('LENGTH(origin_slug) DESC');
    }
}
