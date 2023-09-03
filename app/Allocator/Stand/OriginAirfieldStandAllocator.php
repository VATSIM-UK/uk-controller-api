<?php

namespace App\Allocator\Stand;

use App\Allocator\UsesDestinationStrings;
use App\Models\Vatsim\NetworkAircraft;
use Closure;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class OriginAirfieldStandAllocator implements ArrivalStandAllocator, RankableArrivalStandAllocator
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
            $this->filterQuery($aircraft),
            self::ORDER_BYS,
        );
    }

    public function getRankedStandAllocation(NetworkAircraft $aircraft): Collection
    {
        if (!$aircraft->planned_depairport) {
            return collect();
        }

        return $this->selectRankedStandsUsingStandardConditions(
            $aircraft,
            $this->filterQuery($aircraft),
            self::ORDER_BYS,
        );
    }

    private function filterQuery(NetworkAircraft $aircraft): Closure
    {
        return fn(Builder $query)
            => $query->notCargo()->whereIn('origin_slug', $this->getDestinationStrings($aircraft));
    }
}
