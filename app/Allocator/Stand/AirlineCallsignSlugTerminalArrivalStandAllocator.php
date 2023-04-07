<?php

namespace App\Allocator\Stand;

use App\Models\Vatsim\NetworkAircraft;
use App\Services\AirlineService;
use Illuminate\Database\Eloquent\Builder;

class AirlineCallsignSlugTerminalArrivalStandAllocator extends AbstractArrivalStandAllocator
{
    use UsesCallsignSlugs;

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
            ->whereIn('airline_terminal.callsign_slug', $this->getCallsignSlugs($aircraft))
            ->orderByRaw('airline_terminal.callsign_slug IS NOT NULL')
            ->orderByRaw('LENGTH(airline_terminal.callsign_slug) DESC')
            ->orderBy('airline_terminal.priority');
    }
}
