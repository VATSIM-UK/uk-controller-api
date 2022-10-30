<?php

namespace App\Allocator\Squawk\Local;

use App\Allocator\Squawk\AbstractSquawkAllocator;
use App\Models\Squawk\UnitDiscrete\UnitDiscreteSquawkRange;
use App\Models\Squawk\UnitDiscrete\UnitDiscreteSquawkRangeGuest;
use App\Services\ControllerService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class UnitDiscreteSquawkAllocator extends AbstractSquawkAllocator
{
    protected function getOrderedSquawkRangesQuery(array $details): Builder
    {
        $parsedUnit = ControllerService::getControllerFacilityFromCallsign(
            $details['unit']
        );

        return UnitDiscreteSquawkRange::whereIn('unit', $this->getApplicableUnits($parsedUnit));
    }

    private function getApplicableUnits(string $unit): array
    {
        return array_merge(
            UnitDiscreteSquawkRangeGuest::where('guest_unit', $unit)
                ->pluck('primary_unit')
                ->all(),
            [$unit]
        );
    }

    /**
     * For this particular allocator, we filter the ranges to only those where
     * the flight rules or unit types match.
     */
    protected function filterRanges(Collection $ranges, array $details): Collection
    {
        $details['unit_type'] = isset($details['unit'])
            ? ControllerService::getControllerLevelFromCallsign($details['unit'])
            : '';

        return $ranges->filter(
            fn(UnitDiscreteSquawkRange $range): bool => !$range->hasRule() || $range->rule->passes('', $details)
        );
    }

    protected function canAllocateSquawk(array $details): bool
    {
        return isset($details['unit']);
    }

    protected function getAssignmentType(): string
    {
        return 'UNIT_DISCRETE';
    }
}
