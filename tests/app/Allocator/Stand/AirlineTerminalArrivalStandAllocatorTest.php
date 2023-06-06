<?php

namespace App\Allocator\Stand;

use App\BaseFunctionalTestCase;
use App\Models\Aircraft\WakeCategory;
use App\Models\Airfield\Terminal;
use App\Models\Airline\Airline;
use App\Models\Stand\Stand;
use App\Models\Vatsim\NetworkAircraft;
use Illuminate\Support\Facades\DB;
use util\Traits\WithWakeCategories;

class AirlineTerminalArrivalStandAllocatorTest extends BaseFunctionalTestCase
{
    use WithWakeCategories;

    /**
     * @var AirlineArrivalStandAllocator
     */
    private $allocator;

    public function setUp(): void
    {
        parent::setUp();
        $this->allocator = $this->app->make(AirlineTerminalArrivalStandAllocator::class);
        Airline::where('icao_code', 'BAW')->first()->terminals()->attach(2);
        Stand::find(1)->update(['terminal_id' => 1]);
        Stand::find(2)->update(['terminal_id' => 2]);
    }

    public function testItAllocatesAStandAtTheRightTerminal()
    {
        $aircraft = $this->createAircraft('BAW23451', 'EGLL');
        $this->assertEquals(2, $this->allocator->allocate($aircraft));
    }

    public function testItDoesntAssignTerminalsWithSpecificDestinations()
    {
        Stand::query()->update(['terminal_id' => null]);
        $terminal1 = Terminal::factory()->create(['airfield_id' => 1]);
        Stand::factory()->withTerminal($terminal1)->create(['airfield_id' => 1, 'identifier' => '1A']);
        DB::table('airline_terminal')->insert(
            [
                [
                    'airline_id' => 1,
                    'terminal_id' => $terminal1->id,
                    'destination' => 'EGFF',
                ],
            ]
        );

        $aircraft = $this->createAircraft('BAW23451', 'EGLL');
        $this->assertNull($this->allocator->allocate($aircraft));
    }

    public function testItDoesntAssignTerminalsWithSpecificCallsignSlugs()
    {
        Stand::query()->update(['terminal_id' => null]);
        $terminal1 = Terminal::factory()->create(['airfield_id' => 1]);
        Stand::factory()->withTerminal($terminal1)->create(['airfield_id' => 1, 'identifier' => '1A']);
        DB::table('airline_terminal')->insert(
            [
                [
                    'airline_id' => 1,
                    'terminal_id' => $terminal1->id,
                    'callsign_slug' => '333',
                ],
            ]
        );

        $aircraft = $this->createAircraft('BAW23451', 'EGLL');
        $this->assertNull($this->allocator->allocate($aircraft));
    }

    public function testItAssignsStandsWithSpecificFullCallsigns()
    {
        Stand::query()->update(['terminal_id' => null]);
        $terminal1 = Terminal::factory()->create(['airfield_id' => 1]);
        Stand::factory()->withTerminal($terminal1)->create(['airfield_id' => 1, 'identifier' => '1A']);
        DB::table('airline_terminal')->insert(
            [
                [
                    'airline_id' => 1,
                    'terminal_id' => $terminal1->id,
                    'full_callsign' => '333',
                ],
            ]
        );

        $aircraft = $this->createAircraft('BAW23451', 'EGLL');
        $this->assertNull($this->allocator->allocate($aircraft));
    }

    public function testItDoesntAssignTerminalsWithSpecificAircraftTypes()
    {
        Stand::query()->update(['terminal_id' => null]);
        $terminal1 = Terminal::factory()->create(['airfield_id' => 1]);
        Stand::factory()->withTerminal($terminal1)->create(['airfield_id' => 1, 'identifier' => '1A']);
        DB::table('airline_terminal')->insert(
            [
                [
                    'airline_id' => 1,
                    'terminal_id' => $terminal1->id,
                    'aircraft_id' => 1,
                ],
            ]
        );

        $aircraft = $this->createAircraft('BAW23451', 'EGLL');
        $this->assertNull($this->allocator->allocate($aircraft));
    }


    public function testItAPrefersStandsWithNoSpecificCallsignSlugs()
    {
        Stand::query()->update(['terminal_id' => null]);
        $terminal1 = Terminal::factory()->create(['airfield_id' => 1]);
        $stand1 = Stand::factory()->withTerminal($terminal1)->create(['airfield_id' => 1, 'identifier' => '1B']);
        $terminal2 = Terminal::factory()->create(['airfield_id' => 1]);
        Stand::factory()->withTerminal($terminal2)->create(['airfield_id' => 1, 'identifier' => '1A']);

        DB::table('airline_terminal')->insert(
            [
                [
                    'airline_id' => 1,
                    'terminal_id' => $terminal2->id,
                    'callsign_slug' => '333',
                ],
                [
                    'airline_id' => 1,
                    'terminal_id' => $terminal1->id,
                    'callsign_slug' => null,
                ],
            ]
        );

        $aircraft = $this->createAircraft('BAW23451', 'EGLL');
        $this->assertEquals($stand1->id, $this->allocator->allocate($aircraft));
    }

    public function testItAllocatesStandsInWeightAscendingOrder()
    {
        $this->setWakeCategoryForAircraft('B738', 'S');
        $weightAppropriateStand = Stand::create(
            [
                'airfield_id' => 1,
                'identifier' => '502',
                'latitude' => 54.65875500,
                'longitude' => -6.22258694,
                'wake_category_id' => WakeCategory::where('code', 'S')->first()->id,
                'terminal_id' => 2,
            ]
        );

        $aircraft = $this->createAircraft('BAW23451', 'EGLL');
        $this->assertEquals($weightAppropriateStand->id, $this->allocator->allocate($aircraft));
    }

    public function testItAllocatesStandsAtAppropriateWeight()
    {
        $this->setWakeCategoryForAircraft('B738', 'UM');
        $weightAppropriateStand = Stand::create(
            [
                'airfield_id' => 1,
                'identifier' => '502',
                'latitude' => 54.65875500,
                'longitude' => -6.22258694,
                'wake_category_id' => WakeCategory::where('code', 'UM')->first()->id,
                'terminal_id' => 2,
            ]
        );

        $aircraft = $this->createAircraft('BAW23451', 'EGLL');
        $this->assertEquals($weightAppropriateStand->id, $this->allocator->allocate($aircraft));
    }

    public function testItDoesntAllocateOccupiedStands()
    {
        $extraStand = Stand::create(
            [
                'airfield_id' => 1,
                'identifier' => '502',
                'latitude' => 54.65875500,
                'longitude' => -6.22258694,
                'wake_category_id' => WakeCategory::where('code', 'UM')->first()->id,
                'terminal_id' => 2,
            ]
        );

        $occupier = $this->createAircraft('EZY7823', 'EGLL');
        $occupier->occupiedStand()->sync([2]);
        $aircraft = $this->createAircraft('BAW23451', 'EGLL');

        $this->assertEquals($extraStand->id, $this->allocator->allocate($aircraft));
    }

    public function testItDoesntAllocateUnavailableStands()
    {
        $extraStand = Stand::create(
            [
                'airfield_id' => 1,
                'identifier' => '502',
                'latitude' => 54.65875500,
                'longitude' => -6.22258694,
                'wake_category_id' => WakeCategory::where('code', 'UM')->first()->id,
                'terminal_id' => 2,
            ]
        );
        NetworkAircraft::find('BAW123')->occupiedStand()->sync([2]);

        $aircraft = $this->createAircraft('BAW23451', 'EGLL');
        $this->assertEquals($extraStand->id, $this->allocator->allocate($aircraft));
    }

    public function testItDoesntAllocateNonExistentAirlines()
    {
        $aircraft = $this->createAircraft('***1234', 'EGLL');
        $this->assertNull($this->allocator->allocate($aircraft));
    }

    public function testItReturnsNullOnNoStandAllocated()
    {
        Stand::all()->each(function (Stand $stand) {
            $stand->delete();
        });
        $aircraft = $this->createAircraft('BAW999', 'EGLL');
        $this->assertNull($this->allocator->allocate($aircraft));
    }

    private function createAircraft(
        string $callsign,
        string $arrivalAirport,
        string $departureAirport = 'EGGD'
    ): NetworkAircraft {
        return NetworkAircraft::create(
            [
                'callsign' => $callsign,
                'cid' => 1234,
                'planned_aircraft' => 'B738',
                'planned_aircraft_short' => 'B738',
                'planned_destairport' => $arrivalAirport,
                'planned_depairport' => $departureAirport,
            ]
        );
    }
}
