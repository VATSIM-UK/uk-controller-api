<?php

namespace App\Allocator\Squawk\Local;

use App\Allocator\Squawk\SquawkAssignmentCategories;
use App\Allocator\Squawk\SquawkAllocatorInterface;
use App\Allocator\Squawk\SquawkAssignmentInterface;
use App\Models\Squawk\UnitDiscrete\UnitDiscreteSquawkAssignment;
use App\Models\Squawk\UnitDiscrete\UnitDiscreteSquawkRange;
use App\Models\Squawk\UnitDiscrete\UnitDiscreteSquawkRangeGuest;
use App\Models\Vatsim\NetworkAircraft;
use App\Services\ControllerService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UnitDiscreteSquawkAllocator implements SquawkAllocatorInterface
{
    private function getApplicableRanges(string $unit, array $details): Collection
    {
        $ranges = UnitDiscreteSquawkRange::with('rules')
            ->whereIn('unit', $this->getApplicableUnits($unit))
            ->get();
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

    private function getApplicableUnits(string $unit): array
    {
        return array_merge(
            UnitDiscreteSquawkRangeGuest::where('guest_unit', $unit)
                ->pluck('primary_unit')
                ->all(),
            [$unit]
        );
    }

    public function allocate(string $callsign, array $details): ?SquawkAssignmentInterface
    {
        $unit = isset($details['unit']) ? ControllerService::getControllerFacilityFromCallsign(
            $details['unit']
        ) : null;
        if (!$unit) {
            Log::error('Unit not provided for local squawk assignment');
            return null;
        }

        // Add the unit level for validation rules
        $details['unit_type'] = isset($details['unit'])
            ? ControllerService::getControllerLevelFromCallsign($details['unit'])
            : '';

        $assignment = null;

        DB::transaction(
            function () use (&$assignment, $callsign, $details, $unit) {
                $this->getApplicableRanges($unit, $details)->each(
                    function (UnitDiscreteSquawkRange $range) use (
                        &$assignment,
                        $callsign
                    ) {
                        // Lock the range to prevent duplicate inserts
                        UnitDiscreteSquawkRange::lockForUpdate()->find($range->id);

                        $allSquawks = $range->getAllSquawksInRange();
                        $possibleSquawks = $allSquawks->diff(
                            UnitDiscreteSquawkAssignment::whereIn('code', $allSquawks)
                                ->where('unit', $range->unit)
                                ->pluck('code')
                                ->all()
                        );

                        if ($possibleSquawks->isEmpty()) {
                            return true;
                        }

                        NetworkAircraft::firstOrCreate(
                            [
                                'callsign' => $callsign,
                            ]
                        );

                        $assignment = UnitDiscreteSquawkAssignment::create(
                            [
                                'callsign' => $callsign,
                                'unit' => $range->unit,
                                'code' => $possibleSquawks->first(),
                            ]
                        );
                        return false;
                    }
                );
            }
        );

        return $assignment;
    }

    public function delete(string $callsign): bool
    {
        return UnitDiscreteSquawkAssignment::destroy($callsign);
    }

    public function fetch(string $callsign): ?SquawkAssignmentInterface
    {
        return UnitDiscreteSquawkAssignment::find($callsign);
    }

    public function canAllocateForCategory(string $category): bool
    {
        return $category === SquawkAssignmentCategories::LOCAL;
    }
}
