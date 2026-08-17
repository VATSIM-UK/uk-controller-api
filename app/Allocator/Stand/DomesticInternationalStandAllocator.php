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

    public function allocate(
        NetworkAircraft $aircraft,
        StandAllocationType $type = StandAllocationType::Arrival
    ): ?int {
        if ($this->comparisonAirfield($aircraft, $type) === null || !$aircraft->aircraft_id) {
            return null;
        }

        return $this->selectStandsUsingStandardConditions(
            $aircraft,
            $this->queryFilter($aircraft, $type),
            [],
            true,
            $type
        );
    }

    public function getRankedStandAllocation(NetworkAircraft $aircraft): Collection
    {
        if ($this->comparisonAirfield($aircraft, StandAllocationType::Arrival) === null || !$aircraft->aircraft_id) {
            return collect();
        }

        return $this->selectRankedStandsUsingStandardConditions(
            $aircraft,
            $this->queryFilter($aircraft, StandAllocationType::Arrival)
        );
    }

    private function queryFilter(NetworkAircraft $aircraft, StandAllocationType $type): Closure
    {
        return fn (Builder $query) => $this->getDomesticInternationalScope($aircraft, $query, $type);
    }

    protected function getDomesticInternationalScope(
        NetworkAircraft $aircraft,
        Builder $builder,
        StandAllocationType $type = StandAllocationType::Arrival
    ): Builder {
        return $this->isDomestic($aircraft, $type)
            ? $builder->domestic()
            : $builder->international();
    }

    private function isDomestic(NetworkAircraft $aircraft, StandAllocationType $type): bool
    {
        return Str::startsWith($this->comparisonAirfield($aircraft, $type), ['EG', 'EI']);
    }

    private function comparisonAirfield(NetworkAircraft $aircraft, StandAllocationType $type): ?string
    {
        return ($type === StandAllocationType::Arrival
            ? $aircraft->planned_depairport
            : $aircraft->planned_destairport) ?: null;
    }
}
