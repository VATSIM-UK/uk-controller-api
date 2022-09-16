<?php

namespace App\Filament\Helpers;

use App\BaseFunctionalTestCase;
use App\Models\Aircraft\Aircraft;
use App\Models\Airfield\Airfield;
use App\Models\Airline\Airline;
use App\Models\Controller\ControllerPosition;
use Illuminate\Support\Facades\Cache;

class SelectOptionsTest extends BaseFunctionalTestCase
{
    public function setUp(): void
    {
        parent::setUp();
        SelectOptions::clearAllCaches();
    }

    public function testItGetsAndCachesAircraftTypes()
    {
        $expected = collect([
            1 => 'B738',
            2 => 'A333',
        ]);

        $this->assertEquals($expected, SelectOptions::aircraftTypes());
        $this->assertEquals($expected, Cache::get('SELECT_OPTIONS_AIRCRAFT_TYPES'));
    }

    public function testItGetsCachedAircraftTypesWithoutQuerying()
    {
        $expected = collect([
            1 => 'B738',
            2 => 'A333',
        ]);

        Cache::forever('SELECT_OPTIONS_AIRCRAFT_TYPES', $expected);
        Aircraft::withoutEvents(function () {
            Aircraft::where('id', 1)->update(['code' => 'B744']);
        });

        $this->assertEquals($expected, SelectOptions::aircraftTypes());
    }

    public function testDeletingAnAircraftTypeRebuildsTheCache()
    {
        $expected = collect([
            1 => 'B738',
        ]);

        Aircraft::findOrFail(2)->delete();
        $this->assertEquals($expected, SelectOptions::aircraftTypes());
        $this->assertEquals($expected, Cache::get('SELECT_OPTIONS_AIRCRAFT_TYPES'));
    }

    public function testCreatingAnAircraftTypeRebuildsTheCache()
    {
        $newAircraft = Aircraft::create(
            [
                'code' => 'B739',
                'allocate_stands' => true,
                'wingspan' => 117.83,
                'length' => 129.50,
            ]
        );

        $expected = collect([
            1 => 'B738',
            2 => 'A333',
            $newAircraft->id => 'B739',
        ]);

        $this->assertEquals($expected, SelectOptions::aircraftTypes());
        $this->assertEquals($expected, Cache::get('SELECT_OPTIONS_AIRCRAFT_TYPES'));
    }

    public function testUpdatingAnAircraftTypeRebuildsTheCache()
    {
        $expected = collect([
            1 => 'B788',
            2 => 'A333',
        ]);

        Aircraft::where('id', 1)->update(['code' => 'B788']);
        $this->assertEquals($expected, SelectOptions::aircraftTypes());
        $this->assertEquals($expected, Cache::get('SELECT_OPTIONS_AIRCRAFT_TYPES'));
    }

    public function testItGetsAndCachesAirfields()
    {
        $expected = collect([
            1 => 'EGLL',
            2 => 'EGBB',
            3 => 'EGKR',
        ]);

        $this->assertEquals($expected, SelectOptions::airfields());
        $this->assertEquals($expected, Cache::get('SELECT_OPTIONS_AIRFIELDS'));
    }

    public function testItGetsCachedAirfieldsWithoutQuerying()
    {
        $expected = collect([
            1 => 'EGLL',
            2 => 'EGBB',
            3 => 'EGKR',
        ]);

        Cache::forever('SELECT_OPTIONS_AIRFIELDS', $expected);
        Airfield::withoutEvents(function () {
            Airfield::where('id', 1)->update(['code' => 'EGLK']);
        });

        $this->assertEquals($expected, SelectOptions::airfields());
    }

    public function testDeletingAnAirfieldRebuildsTheCache()
    {
        $expected = collect([
            1 => 'EGLL',
            2 => 'EGBB',
        ]);

        Airfield::findOrFail(3)->delete();
        $this->assertEquals($expected, SelectOptions::airfields());
        $this->assertEquals($expected, Cache::get('SELECT_OPTIONS_AIRFIELDS'));
    }

    public function testCreatingAnAirfieldRebuildsTheCache()
    {
        $newAirfield = Airfield::factory()->create();

        $expected = collect([
            1 => 'EGLL',
            2 => 'EGBB',
            3 => 'EGKR',
            $newAirfield->id => $newAirfield->code,
        ]);

        $this->assertEquals($expected, SelectOptions::airfields());
        $this->assertEquals($expected, Cache::get('SELECT_OPTIONS_AIRFIELDS'));
    }

    public function testUpdatingAnAirfieldRebuildsTheCache()
    {
        $expected = collect([
            1 => 'EGLK',
            2 => 'EGBB',
            3 => 'EGKR',
        ]);

        Airfield::where('id', 1)->update(['code' => 'EGLK']);
        $this->assertEquals($expected, SelectOptions::airfields());
        $this->assertEquals($expected, Cache::get('SELECT_OPTIONS_AIRFIELDS'));
    }

    public function testItGetsAndCachesAirlines()
    {
        $expected = collect([
            1 => 'BAW',
            2 => 'SHT',
            3 => 'VIR',
        ]);

        $this->assertEquals($expected, SelectOptions::airlines());
        $this->assertEquals($expected, Cache::get('SELECT_OPTIONS_AIRLINES'));
    }

    public function testItGetsCachedAirlinesWithoutQuerying()
    {
        $expected = collect([
            1 => 'BAW',
            2 => 'SHT',
            3 => 'VIR',
        ]);

        Cache::forever('SELECT_OPTIONS_AIRLINES', $expected);
        Airline::withoutEvents(function () {
            Airline::where('id', 1)->update(['icao_code' => 'LOL']);
        });

        $this->assertEquals($expected, SelectOptions::airlines());
    }

    public function testDeletingAnAirlineRebuildsTheCache()
    {
        $expected = collect([
            1 => 'BAW',
            2 => 'SHT',
        ]);

        Airline::findOrFail(3)->delete();
        $this->assertEquals($expected, SelectOptions::airlines());
        $this->assertEquals($expected, Cache::get('SELECT_OPTIONS_AIRLINES'));
    }

    public function testCreatingAnAirlineRebuildsTheCache()
    {
        $newAirline = Airline::create([
            'icao_code' => 'EZY',
            'name' => 'Easyjet',
            'callsign' => 'EASY',
        ]);

        $expected = collect([
            1 => 'BAW',
            2 => 'SHT',
            3 => 'VIR',
            $newAirline->id => $newAirline->icao_code,
        ]);

        $this->assertEquals($expected, SelectOptions::airlines());
        $this->assertEquals($expected, Cache::get('SELECT_OPTIONS_AIRLINES'));
    }

    public function testUpdatingAnAirlineRebuildsTheCache()
    {
        $expected = collect([
            1 => 'LOL',
            2 => 'SHT',
            3 => 'VIR',
        ]);

        Airline::where('id', 1)->update(['icao_code' => 'LOL']);
        $this->assertEquals($expected, SelectOptions::airlines());
        $this->assertEquals($expected, Cache::get('SELECT_OPTIONS_AIRLINES'));
    }

    public function testItGetsAndCachesControllers()
    {
        $expected = collect([
            1 => 'EGLL_S_TWR',
            2 => 'EGLL_N_APP',
            3 => 'LON_S_CTR',
            4 => 'LON_C_CTR',
        ]);

        $this->assertEquals($expected, SelectOptions::controllers());
        $this->assertEquals($expected, Cache::get('SELECT_OPTIONS_CONTROLLER_POSITIONS'));
    }

    public function testItGetsCachedControllersWithoutQuerying()
    {
        $expected = collect([
            1 => 'EGLL_S_TWR',
            2 => 'EGLL_N_APP',
            3 => 'LON_S_CTR',
            4 => 'LON_C_CTR',
        ]);

        Cache::forever('SELECT_OPTIONS_CONTROLLER_POSITIONS', $expected);
        ControllerPosition::withoutEvents(function () {
            ControllerPosition::where('id', 1)->update(['callsign' => 'LOL']);
        });

        $this->assertEquals($expected, SelectOptions::controllers());
    }

    public function testDeletingAControllerRebuildsTheCache()
    {
        $expected = collect([
            1 => 'EGLL_S_TWR',
            2 => 'EGLL_N_APP',
            3 => 'LON_S_CTR',
        ]);

        ControllerPosition::findOrFail(4)->delete();
        $this->assertEquals($expected, SelectOptions::controllers());
        $this->assertEquals($expected, Cache::get('SELECT_OPTIONS_CONTROLLER_POSITIONS'));
    }

    public function testCreatingAControllerRebuildsTheCache()
    {
        $newController = ControllerPosition::factory()->create();

        $expected = collect([
            1 => 'EGLL_S_TWR',
            2 => 'EGLL_N_APP',
            3 => 'LON_S_CTR',
            4 => 'LON_C_CTR',
            $newController->id => $newController->callsign,
        ]);

        $this->assertEquals($expected, SelectOptions::controllers());
        $this->assertEquals($expected, Cache::get('SELECT_OPTIONS_CONTROLLER_POSITIONS'));
    }

    public function testUpdatingAControllerRebuildsTheCache()
    {
        $expected = collect([
            1 => 'LOL',
            2 => 'EGLL_N_APP',
            3 => 'LON_S_CTR',
            4 => 'LON_C_CTR',
        ]);

        ControllerPosition::where('id', 1)->update(['callsign' => 'LOL']);
        $this->assertEquals($expected, SelectOptions::controllers());
        $this->assertEquals($expected, Cache::get('SELECT_OPTIONS_CONTROLLER_POSITIONS'));
    }
}
