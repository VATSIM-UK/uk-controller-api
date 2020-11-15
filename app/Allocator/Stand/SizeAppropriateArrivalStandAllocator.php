<?php

namespace App\Allocator\Stand;

use App\Models\Aircraft\Aircraft;
use App\Models\Vatsim\NetworkAircraft;
use App\Services\AirlineService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class SizeAppropriateArrivalStandAllocator extends AbstractArrivalStandAllocator
{
    /**
     * @var AirlineService
     */
    private $airlineService;

    public function __construct(AirlineService $airlineService)
    {
        $this->airlineService = $airlineService;
    }

    protected function getPossibleStands(NetworkAircraft $aircraft): Collection
    {
        $aircraftType = Aircraft::with('wakeCategory')
            ->where('code', $aircraft->aircraftType)
            ->first();

        // Only allocate a stand if we can match types
        if (!$aircraftType) {
            return new Collection();
        }

        return $this->getArrivalAirfieldStandQuery($aircraft)
            ->whereHas('wakeCategory', function (Builder $builder) use ($aircraftType) {
                $builder->greaterRelativeWeighting($aircraftType->wakeCategory);
            })
                ->get();
    }
}
