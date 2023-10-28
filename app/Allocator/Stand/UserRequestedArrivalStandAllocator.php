<?php

namespace App\Allocator\Stand;

use App\Models\Stand\Stand;
use App\Models\Stand\StandRequest;
use App\Models\Vatsim\NetworkAircraft;
use Illuminate\Database\Eloquent\Builder;

class UserRequestedArrivalStandAllocator implements ArrivalStandAllocator
{
    use SelectsFirstApplicableStand;

    public function allocate(NetworkAircraft $aircraft): ?int
    {
        $requestedStands = StandRequest::where('user_id', $aircraft->cid)
            ->whereHas('stand.airfield', function (Builder $airfield) use ($aircraft) {
                $airfield->where('code', $aircraft->planned_destairport);
            })
            ->whereHas('stand', function (Builder $standQuery) {
                $standQuery->unoccupied()->unassigned();
            })
            ->current()
            ->get();

        if ($requestedStands->isEmpty()) {
            return null;
        }

        return $this->selectFirstStand(
            Stand::whereIn('id', $requestedStands->pluck('stand_id'))
        );
    }
}
