<?php

namespace App\Allocator\Stand;

use App\Allocator\UsesDestinationStrings;
use App\Models\Vatsim\NetworkAircraft;
use App\Services\AirlineService;
use Illuminate\Database\Eloquent\Builder;

class AirlineDestinationTerminalArrivalStandAllocator extends AbstractArrivalStandAllocator
{
    use UsesDestinationStrings;

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

        return $stands->join('terminals', 'terminals.id', '=', 'stands.terminal_id')
            ->join('airline_terminal', 'terminals.id', '=', 'airline_terminal.terminal_id')
            ->where('airline_terminal.airline_id', $airline->id)
            ->whereIn('airline_terminal.destination', $this->getDestinationStrings($aircraft))
            ->orderByRaw('airline_terminal.destination IS NOT NULL')
            ->orderByRaw('LENGTH(airline_terminal.destination) DESC')
            ->orderBy('airline_terminal.priority');
    }
}
