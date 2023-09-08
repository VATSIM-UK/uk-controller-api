<?php

namespace App\Allocator\Stand;

use App\Models\Vatsim\NetworkAircraft;
use Closure;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

trait SelectsFromAirlineSpecificStands
{
    use SelectsStandsUsingStandardConditions;

    /**
     * This method generates a stand query that:
     *
     * - Selects stands that are size appropriate and available
     * - Filters these to stands that are specifically selected for the airline AND a given aircraft type
     * - Applies situational specific filters to the query
     * - Orders these stands by situational specific orders
     * - Orders these stands by the airline's priority for the stand
     * - Orders these stands by the common conditions, minus the general allocation priority
     * (see OrdersStandsByCommonConditions)
     * - Selects the first stand that pops up
     */
    private function selectAirlineSpecificStands(
        NetworkAircraft $aircraft,
        Closure $specificFilters,
        array $specificOrders = []
    ): ?int {
        return $this->selectStandsUsingStandardConditions(
            $aircraft,
            fn (Builder $query) => $specificFilters($query->airline($aircraft->airline_id)),
            array_merge(
                $specificOrders,
                ['airline_stand.priority ASC'],
            ),
            false
        );
    }

    private function selectRankedAirlineSpecificStands(
        NetworkAircraft $aircraft,
        Closure $specificFilters,
        array $specificOrders = []
    ): Collection {
        return $this->selectRankedStandsUsingStandardConditions(
            $aircraft,
            fn (Builder $query) => $specificFilters($query->airline($aircraft->airline_id)),
            array_merge(
                $specificOrders,
                ['airline_stand.priority ASC'],
            ),
            false
        );
    }
}
