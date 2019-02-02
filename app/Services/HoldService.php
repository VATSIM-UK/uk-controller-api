<?php

namespace App\Services;

use App\Models\Hold\Hold;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class HoldService
{
    const CACHE_KEY = 'hold-data';

    /**
     * Returns the current holds in a format
     * that may be converted to a JSON array.
     *
     * @return array
     */
    public function getHolds() : array
    {
        if (Cache::has(self::CACHE_KEY)) {
            return Cache::get(self::CACHE_KEY);
        }

        $data = Hold::all()->toArray();
        Cache::forever(self::CACHE_KEY, $data);
        return $data;
    }

    /**
     * Clear the hold cache
     */
    public function clearCache()
    {
        if (!Cache::forget(self::CACHE_KEY)) {
            Log::warning('Hold cache clear failed');
        }
    }
}
