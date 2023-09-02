<?php

namespace App\Allocator\Stand;

use App\Models\Vatsim\NetworkAircraft;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class DomesticInternationalStandAllocator implements ArrivalStandAllocator
{
    use AppliesOrdering;
    use OrdersStandsByCommonConditions;
    use SelectsFromSizeAppropriateAvailableStands;
    use SelectsFirstApplicableStand;
    use ConsidersStandRequests;

    public function allocate(NetworkAircraft $aircraft): ?int
    {
        if (!$aircraft->planned_depairport) {
            return null;
        }

        return $this->selectFirstStand(
            $this->applyOrderingToStandsQuery(
                $this->joinOtherStandRequests(
                    $this->getDomesticInternationalScope(
                        $aircraft,
                        $this->sizeAppropriateAvailableStandsAtAirfield($aircraft)
                    ),
                    $aircraft
                ),
                $this->commonOrderByConditions
            )
        );
    }

    protected function getDomesticInternationalScope(NetworkAircraft $aircraft, Builder $builder): Builder
    {
        return $this->isDomestic($aircraft)
            ? $builder->domestic()
            : $builder->international();
    }

    private function isDomestic(NetworkAircraft $aircraft): bool
    {
        return Str::startsWith($aircraft->planned_depairport, ['EG', 'EI']);
    }
}
