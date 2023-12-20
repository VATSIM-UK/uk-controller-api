<?php

namespace App\Allocator\Stand;

use App\Models\Vatsim\NetworkAircraft;
use Closure;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

trait SelectsStandsUsingStandardConditions
{
    use SelectsFromSizeAppropriateAvailableStands;
    use OrdersStandsByCommonConditions;
    use AppliesOrdering;
    use SelectsFirstApplicableStand;

    private function selectStandsUsingStandardConditions(
        NetworkAircraft $aircraft,
        Closure $specificFilters,
        array $specificOrders = [],
        bool $includeAssignmentPriority = true
    ): ?int {
        return $this->selectFirstStand(
            $this->standardConditionsStandQuery(
                $aircraft,
                $specificFilters,
                $specificOrders,
                $includeAssignmentPriority,
                false
            )
        );
    }

    private function selectRankedStandsUsingStandardConditions(
        NetworkAircraft $aircraft,
        Closure $specificFilters,
        array $specificOrders = [],
        bool $includeAssignmentPriority = true
    ): Collection {
        $orderByForRankQuery = implode(
            ',',
            $this->orderByForStandsQuery($aircraft, $specificOrders, $includeAssignmentPriority, true)
        );

        return $this->standardConditionsStandQuery(
            $aircraft,
            $specificFilters,
            $specificOrders,
            $includeAssignmentPriority,
            true
        )
            ->selectRaw(sprintf('DENSE_RANK() OVER (ORDER BY %s) AS `rank`', $orderByForRankQuery))
            ->get();
    }

    private function standardConditionsStandQuery(
        NetworkAircraft $aircraft,
        Closure $specificFilters,
        array $specificOrders = [],
        bool $includeAssignmentPriority = true,
        bool $isRanking = false
    ): Builder {
        return $this->applyOrderingToStandsQuery(
            $this->joinOtherStandRequests(
                $specificFilters(
                    $isRanking
                    ? $this->sizeAppropriateAvailableStandsAtAirfieldForRanking($aircraft)
                    : $this->sizeAppropriateAvailableStandsAtAirfield($aircraft)
                ),
                $aircraft
            ),
            $this->orderByForStandsQuery($aircraft, $specificOrders, $includeAssignmentPriority, $isRanking)
        );
    }

    private function orderByForStandsQuery(
        NetworkAircraft $aircraft,
        array $customOrders,
        bool $includeAssignmentPriority,
        bool $isRanking
    ): array {
        /**
         * If we are doing ranking, we don't need to consider stand requests in the priority, nor do we need
         * a random order.
         */
        if ($includeAssignmentPriority) {
            $commonConditions = $isRanking
                ? $this->commonOrderByConditionsForRanking()
                : $this->commonOrderByConditionsForAircraft($aircraft);
        } else {
            $commonConditions = $isRanking
                ? $this->commonOrderByConditionsWithoutAssignmentPriorityForRanking()
                : $this->commonOrderByConditionsWithoutAssignmentPriorityForAircraft($aircraft);
        }

        return array_merge(
            $customOrders,
            $commonConditions
        );
    }

    private function commonOrderByConditionsForAircraft(NetworkAircraft $aircraft): array
    {
        return $aircraft->cid === null
            ? $this->commonOrderByConditionsWithoutRequests()
            : $this->commonOrderByConditions();
    }

    private function commonOrderByConditionsWithoutAssignmentPriorityForAircraft(NetworkAircraft $aircraft): array
    {
        return $aircraft->cid === null
            ? $this->commonOrderByConditionsWithoutRequestsOrAssignmentPriority()
            : $this->commonOrderByConditionsWithoutAssignmentPriority();
    }
}
