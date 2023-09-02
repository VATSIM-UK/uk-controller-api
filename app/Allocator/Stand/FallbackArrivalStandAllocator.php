<?php

namespace App\Allocator\Stand;

use App\Models\Vatsim\NetworkAircraft;

class FallbackArrivalStandAllocator implements ArrivalStandAllocator
{
    use AppliesOrdering;
    use OrdersStandsByCommonConditions;
    use SelectsFromSizeAppropriateAvailableStands;
    use SelectsFirstApplicableStand;
    use ConsidersStandRequests;

    /**
     * This allocator:
     * 
     * - Only allocates stands that are not cargo
     * - Orders by common conditions (see OrdersStandsByCommonConditions)
     * - Selects the first available stand (see SelectsFirstApplicableStand)
     *
     * @param NetworkAircraft $aircraft
     * @return integer|null
     */
    public function allocate(NetworkAircraft $aircraft): ?int
    {
        return $this->selectFirstStand(
            $this->applyOrderingToStandsQuery(
                $this->joinOtherStandRequests(
                    $this->sizeAppropriateAvailableStandsAtAirfield($aircraft)->notCargo(),
                    $aircraft
                ),
                $this->commonOrderByConditions
            )
        );
    }
}
