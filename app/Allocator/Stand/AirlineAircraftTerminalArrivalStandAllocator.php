<?php

namespace App\Allocator\Stand;

use App\Models\Aircraft\Aircraft;
use App\Models\Vatsim\NetworkAircraft;
use App\Services\AirlineService;
use Illuminate\Database\Eloquent\Builder;

class AirlineAircraftTerminalArrivalStandAllocator extends AbstractArrivalStandAllocator
{
    private AirlineService $airlineService;

    public function __construct(AirlineService $airlineService)
    {
        $this->airlineService = $airlineService;
    }

    protected function getOrderedStandsQuery(Builder $stands, NetworkAircraft $aircraft): ?Builder
    {
        $airline = $this->airlineService->getAirlineForAircraft($aircraft);
        if ($airline === null) {
            return null;
        }

        $aircraftType = Aircraft::where('code', $aircraft->planned_aircraft)->first();
        if (!$aircraftType) {
            return null;
        }

        return $stands->join('terminals', 'terminals.id', '=', 'stands.terminal_id')
            ->join('airline_terminal', 'terminals.id', '=', 'airline_terminal.terminal_id')
            ->where('airline_terminal.airline_id', $airline->id)
            ->where('airline_terminal.aircraft_id', $aircraftType->id)
            ->orderBy('airline_terminal.priority');
    }
}
