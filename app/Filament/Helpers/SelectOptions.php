<?php

namespace App\Filament\Helpers;

use App\Models\Aircraft\Aircraft;
use App\Models\Aircraft\WakeCategoryScheme;
use App\Models\Airfield\Airfield;
use App\Models\Airline\Airline;
use App\Models\Controller\ControllerPosition;
use App\Models\Controller\Handoff;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use LogicException;

class SelectOptions
{
    private const MODEL_CACHE_KEYS = [
        Aircraft::class => [SelectOptionCacheKeys::AircraftTypes],
        Airfield::class => [SelectOptionCacheKeys::Airfields],
        Airline::class => [SelectOptionCacheKeys::Airlines],
        ControllerPosition::class => [SelectOptionCacheKeys::ControllerPositions],
        Handoff::class => [SelectOptionCacheKeys::Handoffs, SelectOptionCacheKeys::NonAirfieldHandoffs],
        WakeCategoryScheme::class => [SelectOptionCacheKeys::WakeSchemes],
    ];

    public static function aircraftTypes(): Collection
    {
        return self::getOptions(
            SelectOptionCacheKeys::AircraftTypes,
            fn (): Collection => Aircraft::all()->mapWithKeys(
                fn (Aircraft $aircraft) => [$aircraft->id => $aircraft->code]
            )->toBase()
        );
    }

    public static function airfields(): Collection
    {
        return self::getOptions(
            SelectOptionCacheKeys::Airfields,
            fn (): Collection => Airfield::all()->mapWithKeys(
                fn (Airfield $airfield) => [$airfield->id => $airfield->code]
            )->toBase()
        );
    }

    public static function airlines(): Collection
    {
        return self::getOptions(
            SelectOptionCacheKeys::Airlines,
            fn (): Collection => Airline::all()->mapWithKeys(
                fn (Airline $airline) => [$airline->id => $airline->icao_code]
            )->toBase()
        );
    }

    public static function controllers(): Collection
    {
        return self::getOptions(
            SelectOptionCacheKeys::ControllerPositions,
            fn (): Collection => ControllerPosition::all()->mapWithKeys(
                fn (ControllerPosition $controller) => [$controller->id => $controller->callsign]
            )->toBase()
        );
    }

    public static function wakeSchemes(): Collection
    {
        return self::getOptions(
            SelectOptionCacheKeys::WakeSchemes,
            fn (): Collection => WakeCategoryScheme::all()->mapWithKeys(
                fn (WakeCategoryScheme $scheme) => [$scheme->id => $scheme->name]
            )->toBase()
        );
    }

    public static function handoffs(): Collection
    {
        return self::getOptions(
            SelectOptionCacheKeys::Handoffs,
            fn (): Collection => Handoff::all()->mapWithKeys(
                fn (Handoff $handoff) => [$handoff->id => $handoff->description]
            )->toBase()
        );
    }

    public static function nonAirfieldHandoffs(): Collection
    {
        return self::getOptions(
            SelectOptionCacheKeys::NonAirfieldHandoffs,
            fn (): Collection => Handoff::whereDoesntHave('airfield')
                ->get()
                ->mapWithKeys(
                    fn (Handoff $handoff) => [$handoff->id => $handoff->description]
                )->toBase()
        );
    }

    private static function getOptions(SelectOptionCacheKeys $cacheKey, callable $default): Collection
    {
        return Cache::rememberForever(
            $cacheKey->value,
            $default
        );
    }

    public static function clearCache(string $class): void
    {
        if (!array_key_exists($class, self::MODEL_CACHE_KEYS)) {
            throw new LogicException(sprintf('No select option for class %s', $class));
        }

        self::clearKeysForModel(self::MODEL_CACHE_KEYS[$class]);
    }

    public static function clearKeysForModel(array $keys):void
    {
        foreach ($keys as $key) {
            Cache::forget($key->value);
        }
    }

    public static function clearAllCaches(): void
    {
        foreach (self::MODEL_CACHE_KEYS as $keys) {
            self::clearKeysForModel($keys);
        }
    }
}
