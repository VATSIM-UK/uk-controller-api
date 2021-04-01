<?php

namespace App\Allocator\Squawk\General;

use App\Allocator\Squawk\AbstractSquawkAllocator;
use App\Models\Squawk\Ccams\CcamsSquawkRange;
use Illuminate\Database\Eloquent\Builder;

class CcamsSquawkAllocator extends AbstractSquawkAllocator
{
    protected function getOrderedSquawkRangesQuery(array $details): Builder
    {
        return CcamsSquawkRange::query();
    }

    protected function canAllocateSquawk(array $details): bool
    {
        return true;
    }

    protected function getAssignmentType(): string
    {
        return 'CCAMS';
    }
}
