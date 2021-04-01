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
        $details['unit_type'] = isset($details['unit'])
            ? ControllerService::getControllerLevelFromCallsign($details['unit'])
            : '';

        return UnitDiscreteSquawkRange::with('rules')
            ->whereIn('unit', $this->getApplicableUnits($parsedUnit));
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
        return $ranges->filter(
            function (UnitDiscreteSquawkRange $range) use ($details) {
                if ($range->rules->isEmpty()) {
                    return true;
                }

                foreach ($range->rules as $rule) {
                    if (!$rule->rule->passes('', $details)) {
                        return false;
                    }
                }

                return true;
            }
        );
    }

    protected function canAllocateSquawk(array $details): bool
    {
        return isset($details['unit']);
    }

    protected function getAssignmentType(): string
    {
        return 'UNIT_DISCREET';
    }
}
