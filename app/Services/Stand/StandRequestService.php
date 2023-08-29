<?php

namespace App\Services\Stand;

use App\Models\Stand\StandRequest;
use App\Models\Vatsim\NetworkAircraft;

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
}
