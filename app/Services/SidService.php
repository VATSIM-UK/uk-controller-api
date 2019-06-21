<?php

namespace App\Services;

use App\Models\Airfield;
use App\Models\Sid;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;

class SidService
{
    const DEPENDENCY_CACHE_KEY = 'initial_altitude_dependency';

    /**
     * @return array
     */
    public function getInitialAltitudeDependency() : array
    {
        if (Cache::has(self::DEPENDENCY_CACHE_KEY)) {
            return Cache::get(self::DEPENDENCY_CACHE_KEY);
        }

        $sidGroups = Sid::all()->groupBy('airfield_id');

        $altitudes = [];
        $sidGroups->each(function(Collection $airfieldGroup) use (&$altitudes) {
            $airfieldModel = Airfield::find($airfieldGroup->first()->airfield_id);

            $airfieldGroup->each(function(Sid $sid) use (&$altitudes, $airfieldModel) {
                $altitudes[$airfieldModel->code][$sid->identifier] = $sid->initial_altitude;
            });
        });

        Cache::forever(self::DEPENDENCY_CACHE_KEY, $altitudes);
        return $altitudes;
    }
}
