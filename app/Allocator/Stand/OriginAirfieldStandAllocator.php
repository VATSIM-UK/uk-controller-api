<?php

namespace App\Allocator\Stand;

use App\Allocator\UsesDestinationStrings;
use App\Models\Vatsim\NetworkAircraft;
use Illuminate\Database\Eloquent\Builder;

class OriginAirfieldStandAllocator implements ArrivalStandAllocator
{
    use SelectsStandsUsingStandardConditions;
    use UsesDestinationStrings;

    private const ORDER_BYS = [
        'origin_slug IS NOT NULL',
        'LENGTH(origin_slug) DESC',
    ];

    public function allocate(NetworkAircraft $aircraft): ?int
    {
        if (!$aircraft->planned_depairport) {
            return null;
        }

        return $this->selectStandsUsingStandardConditions(
            $aircraft,
            fn(Builder $query) => $query->whereIn('origin_slug', $this->getDestinationStrings($aircraft))
                ->notCargo(),
            self::ORDER_BYS,
        );
    }
}
