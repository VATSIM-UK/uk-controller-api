<?php

namespace App\Allocator\Stand;

use App\Models\Vatsim\NetworkAircraft;

trait ChecksForBusinessAviationAircraft
{
    protected function isBusinessAviationAircraft(NetworkAircraft $aircraft)
    {
        return $aircraft->aircraft->is_business_aviation ?? false;
    }
}
