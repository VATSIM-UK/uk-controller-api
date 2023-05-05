<?php

namespace App\Allocator\Stand;

use App\Models\StandRequest;
use App\Models\Vatsim\NetworkAircraft;
use Illuminate\Database\Eloquent\Builder;

class UserRequestedArrivalStandAllocator extends AbstractArrivalStandAllocator
{
    protected function getOrderedStandsQuery(Builder $stands, NetworkAircraft $aircraft): ?Builder
    {
        $requestedStands = StandRequest::where('user_id', $aircraft->cid)
            ->get();

        if ($requestedStands->empty()) {
            return null;
        }

        return $stands->whereIn('id', $requestedStands->pluck('id'));
    }
}
