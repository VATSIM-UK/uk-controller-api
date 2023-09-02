<?php

namespace App\Allocator\Stand;

use App\Models\Vatsim\NetworkAircraft;
use Closure;

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
            $this->applyOrderingToStandsQuery(
                $this->joinOtherStandRequests(
                    $specificFilters(
                        $this->sizeAppropriateAvailableStandsAtAirfield($aircraft)
                    ),
                    $aircraft
                ),
                $this->orderByForStandsQuery($specificOrders, $includeAssignmentPriority)
            )
        );
    }

    private function orderByForStandsQuery(array $customOrders, bool $includeAssignmentPriority): array
    {
        return array_merge(
            $customOrders,
            $includeAssignmentPriority
            ? [...$this->commonOrderByConditions]
            : [...$this->commonOrderByConditionsWithoutAssignmentPriority]
        );
    }
}
