<?php

namespace App\Allocator\Stand;

use App\Models\Vatsim\NetworkAircraft;
use Closure;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class DomesticInternationalStandAllocator implements ArrivalStandAllocator, RankableArrivalStandAllocator
{
    use SelectsStandsUsingStandardConditions;

    public function allocate(NetworkAircraft $aircraft): ?int
    {
        if (!$aircraft->planned_depairport || !$aircraft->aircraft_id) {
            return null;
        }

        return $this->selectStandsUsingStandardConditions(
            $aircraft,
            $this->queryFilter($aircraft)
        );
    }

    public function getRankedStandAllocation(NetworkAircraft $aircraft): Collection
    {
        if (!$aircraft->planned_depairport || !$aircraft->aircraft_id) {
            return collect();
        }

        return $this->selectRankedStandsUsingStandardConditions(
            $aircraft,
            $this->queryFilter($aircraft)
        );
    }

    private function queryFilter(NetworkAircraft $aircraft): Closure
    {
        return fn(Builder $query) => $this->getDomesticInternationalScope($aircraft, $query);
    }

    protected function getDomesticInternationalScope(NetworkAircraft $aircraft, Builder $builder): Builder
    {
        return $this->isDomestic($aircraft)
            ? $builder->domestic()
            : $builder->international();
    }

    private function isDomestic(NetworkAircraft $aircraft): bool
    {
        return Str::startsWith($aircraft->planned_depairport, ['EG', 'EI']);
    }
}
