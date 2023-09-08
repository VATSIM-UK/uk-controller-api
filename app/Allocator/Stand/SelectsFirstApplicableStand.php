<?php

namespace App\Allocator\Stand;

use Illuminate\Database\Eloquent\Builder;

trait SelectsFirstApplicableStand
{
    private function selectFirstStand(Builder $query): ?int
    {
        return $query->first()?->id;
    }
}
