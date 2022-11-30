<?php

namespace App\Allocator\Stand;

use App\Models\Vatsim\NetworkAircraft;
use Illuminate\Database\Eloquent\Builder;

class OriginAirfieldStandAllocator extends AbstractArrivalStandAllocator
{
    protected function getOrderedStandsQuery(Builder $stands, NetworkAircraft $aircraft): ? Builder
    {
        return $stands
            ->whereIn('origin_slug', $this->getDestinationStrings($aircraft))
            ->orderByRaw('origin_slug IS NOT NULL')
            ->orderByRaw('LENGTH(origin_slug) DESC');
    }

    public function getDestinationStrings(NetworkAircraft $aircraft): array
    {
        return [
            substr($aircraft->planned_depairport, 0, 1),
            substr($aircraft->planned_depairport, 0, 2),
            substr($aircraft->planned_depairport, 0, 3),
            $aircraft->planned_depairport
        ];
    }
}
