<?php

namespace App\Allocator\Stand;

use App\Models\Vatsim\NetworkAircraft;
use App\Services\AirlineService;

/**
 * The primary arrival stand allocator for cargo. Looks for either a cargo airline
 * OR remarks that indicate cargo in the flightplan. If this is satisfied, it will look
 * for any cargo stand the airline uses and allocate that.
 *
 * This allows airlines that also handle passengers to have stands for their cargo operation.
 */
class CargoFlightPreferredArrivalStandAllocator implements ArrivalStandAllocator
{
    use ChecksForCargoAirlines;
    use AppliesOrdering;
    use OrdersStandsByCommonConditions;
    use SelectsFromSizeAppropriateAvailableStands;
    use SelectsFirstApplicableStand;
    use ConsidersStandRequests;

    private const ORDER_BYS = [
        'airline_stand.priority ASC',
    ];

    private AirlineService $airlineService;

    public function __construct(AirlineService $airlineService)
    {
        $this->airlineService = $airlineService;
    }

    public function allocate(NetworkAircraft $aircraft): ?int
    {

        // If the aircraft doesnt have an airline, we cant allocate a stand
        if (!$this->isCargoAirline($aircraft) && !$this->isCargoFlight($aircraft)) {
            return null;
        }

        return $this->selectFirstStand(
            $this->applyOrderingToStandsQuery(
                $this->joinOtherStandRequests(
                    $this->sizeAppropriateAvailableStandsAtAirfield($aircraft)
                    ->airline($aircraft->airline_id),
                    $aircraft
                )->cargo(),
                array_merge(
                    self::ORDER_BYS,
                    $this->commonOrderByConditions
                )
            )
        );

    }
}
