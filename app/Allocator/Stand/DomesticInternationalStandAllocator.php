<?php

namespace App\Allocator\Stand;

use App\Models\Vatsim\NetworkAircraft;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class DomesticInternationalStandAllocator implements ArrivalStandAllocator
{
    use SelectsStandsUsingStandardConditions;

    public function allocate(NetworkAircraft $aircraft): ?int
    {
        if (!$aircraft->planned_depairport) {
            return null;
        }

        return $this->selectStandsUsingStandardConditions(
            $aircraft,
            fn(Builder $query) => $this->getDomesticInternationalScope($aircraft, $query)
        );
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
