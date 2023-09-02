<?php

namespace App\Allocator\Stand;

use App\Models\Vatsim\NetworkAircraft;
use Illuminate\Database\Eloquent\Builder;

/**
 * A trait that can be used by any allocator that needs to select stands from
 * terminals that are assigned to airlines.
 */
trait SelectsStandsFromAirlineTerminals
{
    private function standsAtAirlineTerminals(
        Builder $stands,
        NetworkAircraft $aircraft
    ): Builder {
        return $stands->join('terminals', 'terminals.id', '=', 'stands.terminal_id')
            ->join('airline_terminal', 'terminals.id', '=', 'airline_terminal.terminal_id')
            ->where('airline_terminal.airline_id', $aircraft->airline_id);
    }
}
