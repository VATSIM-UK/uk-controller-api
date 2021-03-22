<?php

namespace App\Services;

use App\Models\Departure\SidDepartureIntervalGroup;

class DepartureService
{
    public function getDepartureIntervalGroupsDependency(): array
    {
        return SidDepartureIntervalGroup::with('relatedGroups')->get()->toArray();
    }
}
