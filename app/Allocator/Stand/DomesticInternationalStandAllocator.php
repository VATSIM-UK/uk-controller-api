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
        $isForDeparture = $aircraft->isForDeparture ?? false;

        if ($this->comparisonAirfield($aircraft, $isForDeparture) === null || ! $aircraft->aircraft_id) {
            return null;
        }

        return $this->selectStandsUsingStandardConditions(
            $aircraft,
            $this->queryFilter($aircraft, $isForDeparture)
        );
    }

    public function getRankedStandAllocation(NetworkAircraft $aircraft): Collection
    {
        if (! $aircraft->planned_depairport || ! $aircraft->aircraft_id) {
            return collect();
        }

        return $this->selectRankedStandsUsingStandardConditions(
            $aircraft,
            $this->queryFilter($aircraft, false)
        );
    }

    private function queryFilter(NetworkAircraft $aircraft, bool $isForDeparture): Closure
    {
        return fn (Builder $query) => $this->getDomesticInternationalScope($aircraft, $query, $isForDeparture);
    }

    protected function getDomesticInternationalScope(
        NetworkAircraft $aircraft,
        Builder $builder,
        bool $isForDeparture = false
    ): Builder {
        return $this->isDomestic($aircraft, $isForDeparture)
            ? $builder->domestic()
            : $builder->international();
    }

    private function isDomestic(NetworkAircraft $aircraft, bool $isForDeparture): bool
    {
        return Str::startsWith($this->comparisonAirfield($aircraft, $isForDeparture), ['EG', 'EI']);
    }

    private function comparisonAirfield(NetworkAircraft $aircraft, bool $isForDeparture): ?string
    {
        return ($isForDeparture
            ? $aircraft->planned_destairport
            : $aircraft->planned_depairport) ?: null;
    }
}
