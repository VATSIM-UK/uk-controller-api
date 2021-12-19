<?php

namespace App\Allocator\Stand;

use App\Models\Vatsim\NetworkAircraft;
use Illuminate\Support\Str;

trait ChecksForCargoAirlines
{
    protected function isCargoFlight(NetworkAircraft $aircraft)
    {
        return $this->hasCargoRemarks($aircraft);
    }

    protected function isCargoAirline(NetworkAircraft $aircraft)
    {
        return ($airline = $this->airlineService->getAirlineForAircraft($aircraft)) !== null &&
            $airline->is_cargo;
    }

    private function hasCargoRemarks(NetworkAircraft $aircraft): bool
    {
        return isset($aircraft->remarks) &&
            Str::contains(Str::upper($aircraft->remarks), 'RMK/CARGO');
    }
}
