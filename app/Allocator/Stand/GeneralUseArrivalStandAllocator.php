<?php

namespace App\Allocator\Stand;

use App\Models\Vatsim\NetworkAircraft;
use Illuminate\Database\Eloquent\Builder;
class GeneralUseArrivalStandAllocator extends AbstractArrivalStandAllocator
{
    protected function getOrderedStandsQuery(Builder $stands, NetworkAircraft $aircraft): ?Builder
    {
        return $stands->generalUse()->notCargo();
    }
}
