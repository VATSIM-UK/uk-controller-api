<?php

namespace App\Allocator\Squawk\General;

use App\Allocator\Squawk\AbstractSquawkAllocator;
use App\Models\Squawk\AirfieldPairing\AirfieldPairingSquawkRange;
use Illuminate\Database\Eloquent\Builder;

class AirfieldPairingSquawkAllocator extends AbstractSquawkAllocator
{
    /**
     * Returns a list of destination strings to try.
     */
    private function getDestinationStrings(string $destination): array
    {
        return [
            substr($destination, 0, 1),
            substr($destination, 0, 2),
            substr($destination, 0, 3),
            $destination
        ];
    }

    protected function getOrderedSquawkRangesQuery(array $details): Builder
    {
        return AirfieldPairingSquawkRange::where('origin', $details['origin'])
            ->whereIn('destination', $this->getDestinationStrings($details['destination']))
            ->orderByRaw('LENGTH(destination) DESC');
    }

    protected function canAllocateSquawk(array $details): bool
    {
        return isset($details['origin'], $details['destination']);
    }

    protected function getAssignmentType(): string
    {
        return 'AIRFIELD_PAIR';
    }
}
