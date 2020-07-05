<?php

namespace App\Allocator\Squawk\General;

use App\Allocator\Squawk\SquawkAllocationInterface;

interface SquawkAllocatorInterface
{
    public function generate(array $details): ?SquawkAllocationInterface;
}
