<?php

namespace App\Allocator\Stand;

use App\Models\Vatsim\NetworkAircraft;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Str;

class DomesticInternationalStandAllocator extends AbstractArrivalStandAllocator
{
    protected function getPossibleStands(NetworkAircraft $aircraft): Collection
    {
        if (!$aircraft->planned_depairport) {
            return new Collection();
        }

        return $this->getDomesticInternationalScope($aircraft, $this->getArrivalAirfieldStandQuery($aircraft))
            ->generalUse()
            ->get();
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
