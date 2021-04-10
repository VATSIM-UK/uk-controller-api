<?php

namespace App\Allocator\Squawk\General;

use App\Allocator\Squawk\AbstractSquawkAllocator;
use App\Models\Squawk\Orcam\OrcamSquawkRange;
use Illuminate\Database\Eloquent\Builder;

class OrcamSquawkAllocator extends AbstractSquawkAllocator
{
    /**
     * Returns a list of origins strings to try.
     */
    private function getOriginStrings(string $origin): array
    {
        return [
            substr($origin, 0, 1),
            substr($origin, 0, 2),
            substr($origin, 0, 3),
            $origin
        ];
    }

    protected function getOrderedSquawkRangesQuery(array $details): Builder
    {
        return OrcamSquawkRange::whereIn('origin', $this->getOriginStrings($details['origin']))
            ->orderByRaw('LENGTH(origin) DESC');
    }

    protected function canAllocateSquawk(array $details): bool
    {
        return isset($details['origin']);
    }

    protected function getAssignmentType(): string
    {
        return 'ORCAM';
    }
}
