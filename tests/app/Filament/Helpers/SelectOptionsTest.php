<?php

namespace App\Filament\Helpers;

use App\BaseFunctionalTestCase;
use App\Models\Aircraft\Aircraft;
use App\Models\Aircraft\WakeCategoryScheme;
use App\Models\Airfield\Airfield;
use App\Models\Airline\Airline;
use App\Models\Controller\ControllerPosition;
use App\Models\Controller\Handoff;
use App\Models\Runway\Runway;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class SelectOptionsTest extends BaseFunctionalTestCase
{
    public function setUp(): void
    {
        parent::setUp();
        SelectOptions::clearAllCaches();
        DB::table('sid')->update(['handoff_id' => null]);
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
            Aircraft::where('id', 1)->firstOrFail()->update(['code' => 'B744']);
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

        Aircraft::where('id', 1)->firstOrFail()->update(['code' => 'B788']);
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
            Airfield::where('id', 1)->firstOrFail()->update(['code' => 'EGLK']);
        });

        $this->assertEquals($expected, SelectOptions::airfields());
    }

    public function testDeletingAnAirfieldRebuildsTheCache()
    {
        // Get options to build the cache.
        SelectOptions::airfields();

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
        // Get options to build the cache.
        SelectOptions::airfields();

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
        // Get options to build the cache.
        SelectOptions::airfields();

        $expected = collect([
            1 => 'EGLK',
            2 => 'EGBB',
            3 => 'EGKR',
        ]);

        Airfield::where('id', 1)->firstOrFail()->update(['code' => 'EGLK']);
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
            Airline::where('id', 1)->firstOrFail()->update(['icao_code' => 'LOL']);
        });

        $this->assertEquals($expected, SelectOptions::airlines());
    }

    public function testDeletingAnAirlineRebuildsTheCache()
    {
        // Get options to build the cache.
        SelectOptions::airlines();

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
        // Get options to build the cache.
        SelectOptions::airlines();

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
        // Get options to build the cache.
        SelectOptions::airlines();

        $expected = collect([
            1 => 'LOL',
            2 => 'SHT',
            3 => 'VIR',
        ]);

        Airline::where('id', 1)->firstOrFail()->update(['icao_code' => 'LOL']);
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
            ControllerPosition::where('id', 1)->firstOrFail()->update(['callsign' => 'LOL']);
        });

        $this->assertEquals($expected, SelectOptions::controllers());
    }

    public function testDeletingAControllerRebuildsTheCache()
    {
        // Get options to build the cache.
        SelectOptions::controllers();

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
        // Get options to build the cache.
        SelectOptions::controllers();

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
        // Get options to build the cache.
        SelectOptions::controllers();

        $expected = collect([
            1 => 'LOL',
            2 => 'EGLL_N_APP',
            3 => 'LON_S_CTR',
            4 => 'LON_C_CTR',
        ]);

        ControllerPosition::where('id', 1)->firstOrFail()->update(['callsign' => 'LOL']);
        $this->assertEquals($expected, SelectOptions::controllers());
        $this->assertEquals($expected, Cache::get('SELECT_OPTIONS_CONTROLLER_POSITIONS'));
    }

    public function testItGetsAndCachesHandoffs()
    {
        $expected = collect([
            1 => 'foo',
            2 => 'bar',
        ]);

        $this->assertEquals($expected, SelectOptions::handoffs());
        $this->assertEquals($expected, Cache::get('SELECT_OPTIONS_HANDOFFS'));
    }

    public function testItGetsCachedHandoffsWithoutQuerying()
    {
        $expected = collect([
            1 => 'foo',
            2 => 'bar',
        ]);

        Cache::forever('SELECT_OPTIONS_HANDOFFS', $expected);
        Handoff::withoutEvents(function () {
            Handoff::where('id', 1)->firstOrFail()->update(['description' => 'LOL']);
        });

        $this->assertEquals($expected, SelectOptions::handoffs());
    }

    public function testDeletingAHandoffRebuildsTheCache()
    {
        // Get options to build the cache.
        SelectOptions::handoffs();

        $expected = collect([
            1 => 'foo',
        ]);

        Handoff::findOrFail(2)->delete();
        $this->assertEquals($expected, SelectOptions::handoffs());
        $this->assertEquals($expected, Cache::get('SELECT_OPTIONS_HANDOFFS'));
    }

    public function testCreatingAHandoffRebuildsTheCache()
    {
        // Get options to build the cache.
        SelectOptions::handoffs();

        $newHandoff = Handoff::create(['description' => 'lol']);

        $expected = collect([
            1 => 'foo',
            2 => 'bar',
            $newHandoff->id => 'lol',
        ]);

        $this->assertEquals($expected, SelectOptions::handoffs());
        $this->assertEquals($expected, Cache::get('SELECT_OPTIONS_HANDOFFS'));
    }

    public function testUpdatingAHandoffRebuildsTheCache()
    {
        // Get options to build the cache.
        SelectOptions::handoffs();

        $expected = collect([
            1 => 'lol',
            2 => 'bar',
        ]);

        Handoff::where('id', 1)->firstOrFail()->update(['description' => 'lol']);
        $this->assertEquals($expected, SelectOptions::handoffs());
        $this->assertEquals($expected, Cache::get('SELECT_OPTIONS_HANDOFFS'));
    }

    public function testItGetsAndCachesWakeSchemes()
    {
        $expected = collect([
            1 => 'UK',
            2 => 'RECAT-EU',
        ]);

        $this->assertEquals($expected, SelectOptions::wakeSchemes());
        $this->assertEquals($expected, Cache::get('SELECT_OPTIONS_WAKE_SCHEMES'));
    }

    public function testItGetsCachedWakeSchemesWithoutQuerying()
    {
        // Get options to build the cache.
        SelectOptions::wakeSchemes();

        $expected = collect([
            1 => 'UK',
            2 => 'RECAT-EU',
        ]);

        Cache::forever('SELECT_OPTIONS_WAKE_SCHEMES', $expected);
        WakeCategoryScheme::withoutEvents(function () {
            WakeCategoryScheme::where('id', 1)->firstOrFail()->update(['name' => 'LOL']);
        });

        $this->assertEquals($expected, SelectOptions::wakeSchemes());
    }

    public function testDeletingAWakeSchemeRebuildsTheCache()
    {
        // Get options to build the cache.
        SelectOptions::wakeSchemes();

        $expected = collect([
            1 => 'UK',
        ]);

        WakeCategoryScheme::findOrFail(2)->delete();
        $this->assertEquals($expected, SelectOptions::wakeSchemes());
        $this->assertEquals($expected, Cache::get('SELECT_OPTIONS_WAKE_SCHEMES'));
    }

    public function testCreatingAWakeSchemeRebuildsTheCache()
    {
        // Get options to build the cache.
        SelectOptions::wakeSchemes();

        $newScheme = WakeCategoryScheme::create(['key' => 'LOL', 'name' => 'lol']);

        $expected = collect([
            1 => 'UK',
            2 => 'RECAT-EU',
            $newScheme->id => 'lol',
        ]);

        $this->assertEquals($expected, SelectOptions::wakeSchemes());
        $this->assertEquals($expected, Cache::get('SELECT_OPTIONS_WAKE_SCHEMES'));
    }

    public function testUpdatingAWakeSchemeRebuildsTheCache()
    {
        // Get options to build the cache.
        SelectOptions::wakeSchemes();

        $expected = collect([
            1 => 'lol',
            2 => 'RECAT-EU',
        ]);

        WakeCategoryScheme::where('id', 1)->firstOrFail()->update(['name' => 'lol']);
        $this->assertEquals($expected, SelectOptions::wakeSchemes());
        $this->assertEquals($expected, Cache::get('SELECT_OPTIONS_WAKE_SCHEMES'));
    }

    public function testItGetsAndCachesNonAirfieldHandoffs()
    {
        $airfieldHandoff = Handoff::create(['description' => 'Airfield handoff']);
        Airfield::findOrFail(1)->firstOrFail()->update(['handoff_id' => $airfieldHandoff->id]);
        $expected = collect([
            1 => 'foo',
            2 => 'bar',
        ]);

        $this->assertEquals($expected, SelectOptions::nonAirfieldHandoffs());
        $this->assertEquals($expected, Cache::get('SELECT_OPTIONS_NON_AIRFIELD_HANDOFFS'));
    }

    public function testItGetsCachedNonAirfieldHandoffsWithoutQuerying()
    {
        $airfieldHandoff = Handoff::create(['description' => 'Airfield handoff']);
        Airfield::findOrFail(1)->firstOrFail()->update(['handoff_id' => $airfieldHandoff->id]);
        
        $expected = collect([
            1 => 'foo',
            2 => 'bar',
        ]);

        Cache::forever('SELECT_OPTIONS_NON_AIRFIELD_HANDOFFS', $expected);
        Handoff::withoutEvents(function () {
            Handoff::where('id', 1)->firstOrFail()->update(['description' => 'LOL']);
        });

        $this->assertEquals($expected, SelectOptions::nonAirfieldHandoffs());
    }

    public function testDeletingAHandoffRebuildsTheNonAirfieldCache()
    {
        // Get options to build the cache.
        SelectOptions::nonAirfieldHandoffs();

        $airfieldHandoff = Handoff::create(['description' => 'Airfield handoff']);
        Airfield::findOrFail(1)->firstOrFail()->update(['handoff_id' => $airfieldHandoff->id]);
        
        $expected = collect([
            1 => 'foo',
        ]);

        Handoff::findOrFail(2)->delete();
        $this->assertEquals($expected, SelectOptions::nonAirfieldHandoffs());
        $this->assertEquals($expected, Cache::get('SELECT_OPTIONS_NON_AIRFIELD_HANDOFFS'));
    }

    public function testCreatingAHandoffRebuildsNonAirfieldTheCache()
    {
        // Get options to build the cache.
        SelectOptions::nonAirfieldHandoffs();

        $airfieldHandoff = Handoff::create(['description' => 'Airfield handoff']);
        Airfield::findOrFail(1)->firstOrFail()->update(['handoff_id' => $airfieldHandoff->id]);
        
        $newHandoff = Handoff::create(['description' => 'lol']);

        $expected = collect([
            1 => 'foo',
            2 => 'bar',
            $newHandoff->id => 'lol',
        ]);

        $this->assertEquals($expected, SelectOptions::nonAirfieldHandoffs());
        $this->assertEquals($expected, Cache::get('SELECT_OPTIONS_NON_AIRFIELD_HANDOFFS'));
    }

    public function testUpdatingAHandoffRebuildsTheNonAirfieldCache()
    {
        // Get options to build the cache.
        SelectOptions::nonAirfieldHandoffs();

        $airfieldHandoff = Handoff::create(['description' => 'Airfield handoff']);
        Airfield::findOrFail(1)->firstOrFail()->update(['handoff_id' => $airfieldHandoff->id]);
        
        $expected = collect([
            1 => 'lol',
            2 => 'bar',
        ]);

        Handoff::where('id', 1)->firstOrFail()->update(['description' => 'lol']);
        $this->assertEquals($expected, SelectOptions::nonAirfieldHandoffs());
        $this->assertEquals($expected, Cache::get('SELECT_OPTIONS_NON_AIRFIELD_HANDOFFS'));
    }

    public function testItGetsAndCachesRunways()
    {
        $expected = collect([
            1 => 'EGLL - 27L',
            2 => 'EGLL - 09R',
            3 => 'EGBB - 33',
        ]);

        $this->assertEquals($expected, SelectOptions::runways());
        $this->assertEquals($expected, Cache::get('SELECT_OPTIONS_RUNWAYS'));
    }

    public function testItGetsCachedRunwaysWithoutQuerying()
    {
        $expected = collect([
            1 => 'EGLL - 27L',
            2 => 'EGLL - 09R',
            3 => 'EGBB - 33',
        ]);

        Cache::forever('SELECT_OPTIONS_RUNWAYS', $expected);
        Runway::withoutEvents(function () {
            Runway::where('id', 1)->firstOrFail()->update(['identifier' => '23']);
        });

        $this->assertEquals($expected, SelectOptions::runways());
    }

    public function testDeletingARunwayRebuildsTheCache()
    {
        // Get options to build the cache.
        SelectOptions::runways();

        $expected = collect([
            1 => 'EGLL - 27L',
            2 => 'EGLL - 09R',
        ]);

        Runway::findOrFail(3)->delete();
        $this->assertEquals($expected, SelectOptions::runways());
        $this->assertEquals($expected, Cache::get('SELECT_OPTIONS_RUNWAYS'));
    }

    public function testCreatingARunwayRebuildsTheCache()
    {
        // Get options to build the cache.
        SelectOptions::runways();

        $runway = Runway::findOrFail(1);
        $newRunway = $runway->replicate();
        $newRunway->identifier = '27R';
        $newRunway->save();

        $expected = collect([
            1 => 'EGLL - 27L',
            2 => 'EGLL - 09R',
            3 => 'EGBB - 33',
            $newRunway->id => 'EGLL - 27R',
        ]);

        $this->assertEquals($expected, SelectOptions::runways());
        $this->assertEquals($expected, Cache::get('SELECT_OPTIONS_RUNWAYS'));
    }

    public function testUpdatingARunwayRebuildsTheCache()
    {
        // Get options to build the cache.
        SelectOptions::runways();
        $expected = collect([
            1 => 'EGLL - 27R',
            2 => 'EGLL - 09R',
            3 => 'EGBB - 33',
        ]);

        Runway::where('id', 1)->firstOrFail()->update(['identifier' => '27R']);
        $this->assertEquals($expected, SelectOptions::runways());
        $this->assertEquals($expected, Cache::get('SELECT_OPTIONS_RUNWAYS'));
    }
}
