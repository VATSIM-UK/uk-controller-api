<?php

namespace App\Services;

use App\Models\Aircraft\WakeCategoryScheme;

class WakeService
{
    public function getWakeSchemesDependency(): array
    {
        return WakeCategoryScheme::with('categories', 'categories.departureIntervals', 'categories.arrivalIntervals')
            ->orderBy('wake_category_schemes.id')
            ->get()
            ->toArray();
    }
}
