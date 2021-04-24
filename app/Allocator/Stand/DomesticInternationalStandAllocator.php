<?php

namespace App\Allocator\Stand;

use App\Models\Vatsim\NetworkAircraft;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class DomesticInternationalStandAllocator extends AbstractArrivalStandAllocator
{
    protected function getOrderedStandsQuery(Builder $stands, NetworkAircraft $aircraft): ?Builder
    {
        if (!$aircraft->planned_depairport) {
            return null;
        }

        return $this->getDomesticInternationalScope($aircraft, $stands);
    }

    protected function getDomesticInternationalScope(NetworkAircraft $aircraft, Builder $builder): Builder
    {
        return $this->isDomestic($aircraft)
            ? $builder->domestic()
            : $builder->international();
    }

    private function isDomestic(NetworkAircraft $aircraft): bool
    {
        return Str::startsWith($aircraft->planned_depairport, ['EG']);
    }
}
