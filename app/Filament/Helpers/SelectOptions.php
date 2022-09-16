<?php

namespace App\Filament\Helpers;

use App\Models\Aircraft\Aircraft;
use App\Models\Airfield\Airfield;
use App\Models\Airline\Airline;
use App\Models\Controller\ControllerPosition;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use LogicException;

class SelectOptions
{
    private const CACHE_KEYS = [
        Aircraft::class => 'SELECT_OPTIONS_AIRCRAFT_TYPES',
        Airfield::class => 'SELECT_OPTIONS_AIRFIELDS',
        Airline::class => 'SELECT_OPTIONS_AIRLINES',
        ControllerPosition::class => 'SELECT_OPTIONS_CONTROLLER_POSITIONS',
    ];

    public static function aircraftTypes(): Collection
    {
        return self::getOptions(
            Aircraft::class,
            fn(): Collection => Aircraft::all()->mapWithKeys(
                fn(Aircraft $aircraft) => [$aircraft->id => $aircraft->code]
            )->toBase()
        );
    }

    public static function airfields(): Collection
    {
        return self::getOptions(
            Airfield::class,
            fn(): Collection => Airfield::all()->mapWithKeys(
                fn(Airfield $airfield) => [$airfield->id => $airfield->code]
            )->toBase()
        );
    }

    public static function airlines(): Collection
    {
        return self::getOptions(
            Airline::class,
            fn(): Collection => Airline::all()->mapWithKeys(
                fn(Airline $airline) => [$airline->id => $airline->icao_code]
            )->toBase()
        );
    }

    public static function controllers(): Collection
    {
        return self::getOptions(
            ControllerPosition::class,
            fn(): Collection => ControllerPosition::all()->mapWithKeys(
                fn(ControllerPosition $controller) => [$controller->id => $controller->callsign]
            )->toBase()
        );
    }

    private static function getOptions(string $class, callable $default): Collection
    {
        return Cache::rememberForever(
            self::CACHE_KEYS[$class],
            $default
        );
    }

    public static function clearCache(string $class): void
    {
        if (!array_key_exists($class, self::CACHE_KEYS)) {
            throw new LogicException(sprintf('No select option for class %s', $class));
        }

        Cache::forget(self::CACHE_KEYS[$class]);
    }

    public static function clearAllCaches(): void
    {
        foreach (self::CACHE_KEYS as $key) {
            Cache::forget($key);
        }
    }
}
