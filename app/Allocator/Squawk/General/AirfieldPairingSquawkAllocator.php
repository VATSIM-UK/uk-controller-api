<?php

namespace App\Allocator\Squawk\General;

use App\Allocator\Squawk\SquawkAssignmentCategories;
use App\Allocator\Squawk\SquawkAllocatorInterface;
use App\Allocator\Squawk\SquawkAssignmentInterface;
use App\Models\Squawk\AirfieldPairing\AirfieldPairingSquawkAssignment;
use App\Models\Squawk\AirfieldPairing\AirfieldPairingSquawkRange;
use App\Services\NetworkDataService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AirfieldPairingSquawkAllocator implements SquawkAllocatorInterface
{
    private function getPossibleRangesForFlight(string $origin, string $destination): Collection
    {
        return AirfieldPairingSquawkRange::where('origin', $origin)
            ->where('destination', $destination)
            ->get();
    }

    public function allocate(string $callsign, array $details): ?SquawkAssignmentInterface
    {
        if (!isset($details['origin'])) {
            Log::error(
                'Origin not provided for airfield pairing squawk allocation',
                [$callsign, $details]
            );
            return null;
        }

        if (!isset($details['destination'])) {
            Log::error(
                'Destination not provided for airfield pairing squawk allocation',
                [$callsign, $details]
            );
            return null;
        }

        $assignment = null;
        DB::transaction(
            function () use (&$assignment, $callsign, $details) {
                $this->getPossibleRangesForFlight(
                    $details['origin'],
                    $details['destination']
                )->each(
                    function (AirfieldPairingSquawkRange $range) use (&$assignment, $callsign) {
                        // Lock the range to prevent additional inserts on this range
                        AirfieldPairingSquawkRange::lockForUpdate()->find($range->id);

                        $allSquawks = $range->getAllSquawksInRange();
                        $possibleSquawks = $allSquawks->diff(
                            AirfieldPairingSquawkAssignment::whereIn('code', $allSquawks)
                                ->pluck('code')
                                ->all()
                        );

                        if ($possibleSquawks->isEmpty()) {
                            return true;
                        }

                        NetworkDataService::firstOrCreateNetworkAircraft($callsign);
                        $assignment = AirfieldPairingSquawkAssignment::create(
                            [
                                'callsign' => $callsign,
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
        return AirfieldPairingSquawkAssignment::destroy($callsign);
    }

    public function fetch(string $callsign): ?SquawkAssignmentInterface
    {
        return AirfieldPairingSquawkAssignment::find($callsign);
    }

    public function canAllocateForCategory(string $category): bool
    {
        return $category === SquawkAssignmentCategories::GENERAL;
    }
}
