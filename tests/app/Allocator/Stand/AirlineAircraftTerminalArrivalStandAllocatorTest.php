<?php

namespace App\Allocator\Stand;

use App\BaseFunctionalTestCase;
use App\Models\Aircraft\Aircraft;
use App\Models\Airfield\Terminal;
use App\Models\Stand\Stand;
use App\Models\Vatsim\NetworkAircraft;
use Illuminate\Support\Facades\DB;

class AirlineAircraftTerminalArrivalStandAllocatorTest extends BaseFunctionalTestCase
{
    private readonly AirlineAircraftTerminalArrivalStandAllocator $allocator;

    public function setUp(): void
    {
        parent::setUp();
        $this->allocator = $this->app->make(AirlineAircraftTerminalArrivalStandAllocator::class);
    }

    public function testItAllocatesAStandWithAFixedCallsign()
    {
        $terminal1 = Terminal::factory()->create(['airfield_id' => 1]);
        $stand1 = Stand::factory()->withTerminal($terminal1)->create(['airfield_id' => 1, 'identifier' => '1A']);

        // Wrong airfield
        $terminal2 = Terminal::factory()->create(['airfield_id' => 2]);
        Stand::factory()->withTerminal($terminal2)->create(['airfield_id' => 2, 'identifier' => '1A']);

        // This shouldn't get picked, it's not terminal this airline has
        $terminal3 = Terminal::factory()->create(['airfield_id' => 1]);
        Stand::factory()->withTerminal($terminal3)->create(['airfield_id' => 1, 'identifier' => '1B']);

        // This shouldn't get picked, not at a terminal!
        Stand::factory()->create(['airfield_id' => 1, 'identifier' => '1C']);

        DB::table('airline_terminal')->insert(
            [
                // Not picked, null aircraft
                [
                    'airline_id' => 1,
                    'terminal_id' => $terminal1->id,
                    'aircraft_id' => null
                ],
                // Will be picked, matches aircraft type
                [
                    'airline_id' => 1,
                    'terminal_id' => $terminal1->id,
                    'aircraft_id' => 1
                ],
                // Not picked, wrong airport
                [
                    'airline_id' => 1,
                    'terminal_id' => $terminal2->id,
                    'aircraft_id' => 1
                ],
                // Not picked, wrong airline
                [
                    'airline_id' => 2,
                    'terminal_id' => $terminal1->id,
                    'aircraft_id' => 1
                ],
            ]
        );

        $aircraft = $this->createAircraft('BAW23451', 'EGLL', 'EGGD');
        $this->assertEquals($stand1->id, $this->allocator->allocate($aircraft));
    }

    public function testItConsidersAirlinePreferences()
    {
        // Not highest priority
        $terminal1 = Terminal::factory()->create(['airfield_id' => 1]);
        Stand::factory()->withTerminal($terminal1)->create(['airfield_id' => 1, 'identifier' => '1A']);

        // Not highest priority
        $terminal2 = Terminal::factory()->create(['airfield_id' => 1]);
        Stand::factory()->withTerminal($terminal2)->create(['airfield_id' => 1, 'identifier' => '1B']);

        // Right airfield, highest priority, should be picked
        $terminal3 = Terminal::factory()->create(['airfield_id' => 1]);
        $stand3 = Stand::factory()->withTerminal($terminal3)->create(['airfield_id' => 1, 'identifier' => '1C']);

        // Wrong airfield, should not be picked
        $terminal4 = Terminal::factory()->create(['airfield_id' => 2]);
        Stand::factory()->withTerminal($terminal4)->create(['airfield_id' => 2, 'identifier' => '1A']);

        DB::table('airline_terminal')->insert(
            [
                [
                    'airline_id' => 1,
                    'terminal_id' => $terminal1->id,
                    'aircraft_id' => 1,
                    'priority' => 100,
                ],
                [
                    'airline_id' => 1,
                    'terminal_id' => $terminal2->id,
                    'aircraft_id' => 1,
                    'priority' => 3,
                ],
                [
                    'airline_id' => 1,
                    'terminal_id' => $terminal3->id,
                    'aircraft_id' => 1,
                    'priority' => 2,
                ],
                [
                    'airline_id' => 1,
                    'terminal_id' => $terminal3->id,
                    'aircraft_id' => 1,
                    'priority' => 2,
                ],
            ]
        );

        $aircraft = $this->createAircraft('BAW23451', 'EGLL', 'EGGD');
        $this->assertEquals($stand3->id, $this->allocator->allocate($aircraft));
    }

    public function testItAllocatesAStandWithAnAppropriateAerodromeReferenceCode()
    {
        $terminal = Terminal::factory()->create(['airfield_id' => 1]);
        Aircraft::where('code', 'B738')->update(['aerodrome_reference_code' => 'E']);
        $weightAppropriateStand = Stand::create(
            [
                'airfield_id' => 1,
                'identifier' => '502',
                'latitude' => 54.65875500,
                'longitude' => -6.22258694,
                'aerodrome_reference_code' => 'E',
                'terminal_id' => $terminal->id,
            ]
        );

        // Too small, should not get picked
        Stand::create(
            [
                'airfield_id' => 1,
                'terminal_id' => $terminal->id,
                'identifier' => '503',
                'latitude' => 54.65875500,
                'longitude' => -6.22258694,
                'aerodrome_reference_code' => 'B',
            ]
        );

        DB::table('airline_terminal')->insert(
            [
                [
                    'airline_id' => 1,
                    'terminal_id' => $terminal->id,
                    'aircraft_id' => 1,
                ],
            ]
        );
        $aircraft = $this->createAircraft('BAW23451', 'EGLL', 'EGGD');
        $this->assertEquals($weightAppropriateStand->id, $this->allocator->allocate($aircraft));
    }

    public function testItAllocatesAStandInAerodromeReferenceAscendingOrder()
    {
        $terminal = Terminal::factory()->create(['airfield_id' => 1]);
        Aircraft::where('code', 'B738')->update(['aerodrome_reference_code' => 'B']);
        $weightAppropriateStand = Stand::create(
            [
                'airfield_id' => 1,
                'identifier' => '502',
                'latitude' => 54.65875500,
                'longitude' => -6.22258694,
                'aerodrome_reference_code' => 'B',
                'terminal_id' => $terminal->id,
            ]
        );

        // Larger stand, should be ignored
        Stand::create(
            [
                'airfield_id' => 1,
                'terminal_id' => $terminal->id,
                'identifier' => '503',
                'latitude' => 54.65875500,
                'longitude' => -6.22258694,
                'aerodrome_reference_code' => 'E',
            ]
        );

        DB::table('airline_terminal')->insert(
            [
                [
                    'airline_id' => 1,
                    'terminal_id' => $terminal->id,
                    'aircraft_id' => 1,
                ],
            ]
        );

        $aircraft = $this->createAircraft('BAW23451', 'EGLL', 'EGGD');
        $this->assertEquals($weightAppropriateStand->id, $this->allocator->allocate($aircraft));
    }

    public function testItDoesntAllocateOccupiedStands()
    {
        $terminal1 = Terminal::factory()->create(['airfield_id' => 1]);
        $stand1 = Stand::factory()->withTerminal($terminal1)->create();

        // Occupied
        $stand2 = Stand::factory()->withTerminal($terminal1)->create();
        $occupier = $this->createAircraft('EZY7823', 'EGLL', 'EGGD');
        $occupier->occupiedStand()->sync([$stand2->id]);

        DB::table('airline_terminal')->insert(
            [
                [
                    'airline_id' => 1,
                    'terminal_id' => $terminal1->id,
                    'aircraft_id' => 1,
                ],
            ]
        );

        $aircraft = $this->createAircraft('BAW23451', 'EGLL', 'EGGD');
        $this->assertEquals($stand1->id, $this->allocator->allocate($aircraft));
    }

    public function testItDoesntAllocateAStandWithNoAircraftType()
    {
        $terminal1 = Terminal::factory()->create(['airfield_id' => 1]);
        Stand::factory()->withTerminal($terminal1)->create();

        // Occupied
        $stand2 = Stand::factory()->withTerminal($terminal1)->create();
        $occupier = $this->createAircraft('EZY7823', 'EGLL', 'EGGD');
        $occupier->occupiedStand()->sync([$stand2->id]);

        DB::table('airline_terminal')->insert(
            [
                [
                    'airline_id' => 1,
                    'terminal_id' => $terminal1->id,
                    'aircraft_id' => null,
                ],
                [
                    'airline_id' => 2,
                    'terminal_id' => $terminal1->id,
                    'aircraft_id' => 1,
                ],
            ]
        );

        $aircraft = $this->createAircraft('BAW23451', 'EGLL', 'EGGD');
        $this->assertNull($this->allocator->allocate($aircraft));
    }

    public function testItDoesntAllocateAtTheWrongAirfield()
    {
        $terminal1 = Terminal::factory()->create(['airfield_id' => 2]);
        Stand::factory()->withTerminal($terminal1)->create();

        DB::table('airline_terminal')->insert(
            [
                [
                    'airline_id' => 1,
                    'terminal_id' => $terminal1->id,
                    'aircraft_id' => 1,
                ],
            ]
        );

        $aircraft = $this->createAircraft('BAW23451', 'EGLL', 'EGGD');
        $this->assertNull($this->allocator->allocate($aircraft));
    }

    public function testItDoesntAllocateForTheWrongAircraftType()
    {
        $terminal1 = Terminal::factory()->create(['airfield_id' => 1]);
        Stand::factory()->withTerminal($terminal1)->create();

        DB::table('airline_terminal')->insert(
            [
                [
                    'airline_id' => 1,
                    'terminal_id' => $terminal1->id,
                    'aircraft_id' => 2,
                ],
            ]
        );

        $aircraft = $this->createAircraft('BAW23451', 'EGLL', 'EGGD');
        $this->assertNull($this->allocator->allocate($aircraft));
    }

    public function testItDoesntAllocateUnavailableStands()
    {
        $terminal1 = Terminal::factory()->create(['airfield_id' => 1]);
        $stand1 = Stand::factory()->withTerminal($terminal1)->create();
        $stand2 = Stand::factory()->withTerminal($terminal1)->create();
        NetworkAircraft::find('BAW123')->occupiedStand()->sync([$stand1->id]);

        DB::table('airline_terminal')->insert(
            [
                [
                    'airline_id' => 1,
                    'terminal_id' => $terminal1->id,
                    'aircraft_id' => 1,
                ],
            ]
        );

        $aircraft = $this->createAircraft('BAW23451', 'EGLL', 'EGGD');
        $this->assertEquals($stand2->id, $this->allocator->allocate($aircraft));
    }

    public function testItDoesntAllocateNonExistentAirlines()
    {
        $terminal1 = Terminal::factory()->create(['airfield_id' => 1]);
        Stand::factory()->withTerminal($terminal1)->create();

        DB::table('airline_terminal')->insert(
            [
                [
                    'airline_id' => 1,
                    'terminal_id' => $terminal1->id,
                    'aircraft_id' => 1,
                ],
                [
                    'airline_id' => 2,
                    'terminal_id' => $terminal1->id,
                    'aircraft_id' => 1,
                ],
            ]
        );
        $aircraft = $this->createAircraft('***1234', 'EGLL', 'EGGD');
        $this->assertNull($this->allocator->allocate($aircraft));
    }

    private function createAircraft(
        string $callsign,
        string $arrivalAirport,
        string $departureAirport
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
