<?php

namespace App\Allocator\Stand;

use App\Models\Vatsim\NetworkAircraft;
use Closure;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

/**
 * A trait that can be used by any allocator that needs to select stands from
 * terminals that are assigned to airlines.
 */
trait SelectsStandsFromAirlineSpecificTerminals
{
    use SelectsStandsUsingStandardConditions;

    /**
     * This method generates a stand query that:
     *
     * - Selects stands that are size appropriate and available
     * - Filters these to stands that are at terminals specifically selected for the airline AND a given aircraft type
     * - Applies situational specific filters to the query
     * - Orders these stands by situational specific orders
     * - Orders these stands by the airline's priority for the stand
     * - Orders these stands by the common conditions, minus the general allocation priority
     * (see OrdersStandsByCommonConditions)
     * - Selects the first stand that pops up
     */
    private function selectStandsAtAirlineSpecificTerminals(
        NetworkAircraft $aircraft,
        Closure $specificFilters,
        array $specificOrders = []
    ): ?int {
        return $this->selectStandsUsingStandardConditions(
            $aircraft,
            fn (Builder $query) => $specificFilters($query->join('terminals', 'terminals.id', '=', 'stands.terminal_id')
                ->join('airline_terminal', 'terminals.id', '=', 'airline_terminal.terminal_id')
                ->where('airline_terminal.airline_id', $aircraft->airline_id)),
            array_merge(
                $specificOrders,
                ['airline_terminal.priority ASC'],
            ),
            false
        );
    }

    private function selectRankedStandsAtAirlineSpecificTerminals(
        NetworkAircraft $aircraft,
        Closure $specificFilters,
        array $specificOrders = []
    ): Collection {
        return $this->selectRankedStandsUsingStandardConditions(
            $aircraft,
            fn (Builder $query) => $specificFilters($query->join('terminals', 'terminals.id', '=', 'stands.terminal_id')
                ->join('airline_terminal', 'terminals.id', '=', 'airline_terminal.terminal_id')
                ->where('airline_terminal.airline_id', $aircraft->airline_id)),
            array_merge(
                $specificOrders,
                ['airline_terminal.priority ASC'],
            ),
            false
        );
    }
}
