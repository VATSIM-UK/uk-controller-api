<?php

namespace App\Services;

use App\Models\Aircraft\Aircraft;
use App\Models\Aircraft\WakeCategory;
use Illuminate\Support\Facades\Cache;

class AircraftService
{
    private const AIRCRAFT_CODE_ID_MAP_CACHE_KEY = 'AIRCRAFT_CODE_ID_MAP';

    public function getAircraftDependency(): array
    {
        return Aircraft::with('wakeCategories')->get()->map(fn (Aircraft $aircraft) => [
            'id' => $aircraft->id,
            'icao_code' => $aircraft->code,
            'wake_categories' => $aircraft->wakeCategories->map(
                fn (WakeCategory $category) => $category->id
            )->toArray(),
        ])->toArray();
    }

    public function getAircraftIdFromCode(string $code): ?int
    {
        return $this->aircraftCodeIdMap()[$code] ?? null;
    }

    public function aircraftDataUpdated(): void
    {
        Cache::forget(self::AIRCRAFT_CODE_ID_MAP_CACHE_KEY);
    }

    private function aircraftCodeIdMap(): array
    {
        return Cache::rememberForever(
            self::AIRCRAFT_CODE_ID_MAP_CACHE_KEY,
            fn () => Aircraft::all()->mapWithKeys(fn (Aircraft $aircraft) => [
                $aircraft->code => $aircraft->id,
            ])->toArray()
        );
    }
}
