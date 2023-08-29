<?php

namespace App\Services\Stand;

use App\Models\Stand\StandRequest;
use App\Models\Vatsim\NetworkAircraft;
use Illuminate\Support\Collection;

class StandRequestService
{
    public function activeRequestForAircraft(NetworkAircraft $aircraft): ?StandRequest
    {
        if (!$aircraft->cid) {
            return null;
        }

        return StandRequest::with('stand')
            ->current()
            ->where('user_id', $aircraft->cid)
            ->where('callsign', $aircraft->callsign)
            ->first();
    }

    public function allActiveStandRequestsForAirfield(string $airfield): Collection
    {
        return StandRequest::with('stand')
            ->current()
            ->whereHas('stand.airfield', function ($query) use ($airfield)
            {
                $query->where('code', $airfield);
            })
            ->orderBy('id')
            ->get();
    }
}
