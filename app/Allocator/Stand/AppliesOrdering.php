<?php

namespace App\Allocator\Stand;

use Illuminate\Database\Eloquent\Builder;

trait AppliesOrdering
{
    private function applyOrderingToStandsQuery(Builder $stands, array $orderBys): Builder
    {
        return array_reduce(
            $orderBys,
            fn (Builder $query, string $orderBy) => $query->orderByRaw($orderBy),
            $stands
        );
    }
}
