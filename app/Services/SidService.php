<?php

namespace App\Services;

use App\Models\Airfield\Airfield;
use App\Models\Sid;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

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
        $sidGroups->each(function (Collection $airfieldGroup) use (&$altitudes) {
            $airfieldModel = Airfield::find($airfieldGroup->first()->airfield_id);

            $airfieldGroup->each(function (Sid $sid) use (&$altitudes, $airfieldModel) {
                $altitudes[$airfieldModel->code][$sid->identifier] = $sid->initial_altitude;
            });
        });

        Cache::forever(self::DEPENDENCY_CACHE_KEY, $altitudes);
        Log::info('Regenerated initial altitude dependency');
        return $altitudes;
    }

    /**
     * @return array
     */
    public function getAllSids() : array
    {
        return Sid::all()->toArray();
    }

    /**
     * @param int $id
     * @return array
     */
    public function getSid(int $id) : ?array
    {
        $sid = Sid::find($id);
        return $sid ? $sid->toArray() : null;
    }

    /**
     * @param int $sidId
     * @return bool
     */
    public function deleteSid(int $sidId) : bool
    {
        $sid = Sid::find($sidId);
        return $sid ? $sid->delete() : false;
    }

    /**
     * @param int $airfieldId
     * @param string $identifier
     * @param int $initialAltitude
     */
    public function createSid(int $airfieldId, string $identifier, int $initialAltitude) : void
    {
        Sid::create(
            [
                'airfield_id' => $airfieldId,
                'identifier' => $identifier,
                'initial_altitude' => $initialAltitude,
                'created_at' => Carbon::now(),
            ]
        );

        Cache::forget(self::DEPENDENCY_CACHE_KEY);
    }

    /**
     * @param int $id
     * @param int $airfieldId
     * @param string $identifier
     * @param int $initialAltitude
     */
    public function updateSid(int $id, int $airfieldId, string $identifier, int $initialAltitude) : void
    {
        $sid = Sid::find($id);
        $sid->airfield_id = $airfieldId;
        $sid->identifier = $identifier;
        $sid->initial_altitude = $initialAltitude;
        $sid->save();

        Cache::forget(self::DEPENDENCY_CACHE_KEY);
    }
}
