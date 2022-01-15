<?php

namespace App\Services;

use App\Models\Aircraft\Aircraft;
use App\Models\Aircraft\WakeCategory;

class AircraftService
{
    public function getAircraftDependency(): array
    {
        return Aircraft::with('wakeCategories')->get()->map(fn(Aircraft $aircraft) => [
            'id' => $aircraft->id,
            'icao_code' => $aircraft->code,
            'wake_categories' => $aircraft->wakeCategories->map(fn(WakeCategory $category) => $category->id)->toArray(),
        ])->toArray();
    }
}
