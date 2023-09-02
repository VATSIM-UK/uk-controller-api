<?php

namespace App\Allocator\Stand;

use App\Allocator\UsesDestinationStrings;
use App\Models\Vatsim\NetworkAircraft;
use Illuminate\Database\Eloquent\Builder;

class OriginAirfieldStandAllocator implements ArrivalStandAllocator
{
    use UsesDestinationStrings;
    use AppliesOrdering;
    use OrdersStandsByCommonConditions;
    use SelectsFromSizeAppropriateAvailableStands;
    use SelectsFirstApplicableStand;
    use ConsidersStandRequests;

    private const ORDER_BYS = [
        'origin_slug IS NOT NULL',
        'LENGTH(origin_slug) DESC',
    ];

    public function allocate(NetworkAircraft $aircraft): ?int
    {
        if (!$aircraft->planned_depairport) {
            return null;
        }

        return $this->selectFirstStand(
            $this->applyOrderingToStandsQuery(
                $this->joinOtherStandRequests(
                    $this->sizeAppropriateAvailableStandsAtAirfield($aircraft)
                        ->whereIn('origin_slug', $this->getDestinationStrings($aircraft))
                        ->notCargo(),
                    $aircraft
                ),
                array_merge(self::ORDER_BYS, $this->commonOrderByConditions)
            )
        );
    }
}
