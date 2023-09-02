<?php

namespace App\Allocator\Stand;

use App\BaseFunctionalTestCase;
use App\Models\Aircraft\Aircraft;
use App\Models\Airline\Airline;
use App\Models\Stand\Stand;
use App\Models\Vatsim\NetworkAircraft;
use Illuminate\Support\Facades\DB;

class AirlineCallsignArrivalStandAllocatorTest extends BaseFunctionalTestCase
{
    private readonly AirlineCallsignArrivalStandAllocator $allocator;

    public function setUp(): void
    {
        parent::setUp();
        $this->allocator = $this->app->make(AirlineCallsignArrivalStandAllocator::class);
        Airline::factory()->create(['icao_code' => 'EZY']);
    }

    public function testItAllocatesAStandWithAFixedCallsign()
    {
        DB::table('airline_stand')->insert(
            [
                [
                    'airline_id' => 1,
                    'stand_id' => 1,
                    'full_callsign' => null
                ],
                [
                    'airline_id' => 1,
                    'stand_id' => 2,
                    'full_callsign' => '23451'
                ],
                [
                    'airline_id' => 1,
                    'stand_id' => 3,
                    'full_callsign' => null
                ],
                [
                    'airline_id' => 2,
                    'stand_id' => 1,
                    'full_callsign' => '23451'
                ],
            ]
        );
        $aircraft = $this->createAircraft('BAW23451', 'EGLL', 'EGGD');
        $this->assertEquals(2, $this->allocator->allocate($aircraft));
    }

    public function testItConsidersAirlinePreferences()
    {
        DB::table('airline_stand')->insert(
            [
                [
                    'airline_id' => 1,
                    'stand_id' => 1,
                    'full_callsign' => '23451',
                    'priority' => 100,
                ],
                [
                    'airline_id' => 1,
                    'stand_id' => 2,
                    'full_callsign' => '23451',
                    'priority' => 3,
                ],
                [
                    'airline_id' => 1,
                    'stand_id' => 3,
                    'full_callsign' => '23451',
                    'priority' => 2,
                ],
                [
                    'airline_id' => 2,
                    'stand_id' => 1,
                    'full_callsign' => '23451',
                    'priority' => 1,
                ],
            ]
        );
        $aircraft = $this->createAircraft('BAW23451', 'EGLL', 'EGGD');
        $this->assertEquals(2, $this->allocator->allocate($aircraft));
    }

    public function testItAllocatesAStandWithAnAppropriateAerodromeReferenceCode()
    {
        Aircraft::where('code', 'B738')->update(['aerodrome_reference_code' => 'E']);
        $weightAppropriateStand = Stand::create(
            [
                'airfield_id' => 1,
                'identifier' => '502',
                'latitude' => 54.65875500,
                'longitude' => -6.22258694,
                'aerodrome_reference_code' => 'E',
            ]
        );
        DB::table('airline_stand')->insert(
            [
                [
                    'airline_id' => 1,
                    'stand_id' => 1,
                    'full_callsign' => null
                ],
                [
                    'airline_id' => 1,
                    'stand_id' => 2,
                    'full_callsign' => '23451'
                ],
                [
                    'airline_id' => 1,
                    'stand_id' => 3,
                    'full_callsign' => null
                ],
                [
                    'airline_id' => 2,
                    'stand_id' => 1,
                    'full_callsign' => '23451'
                ],
                [
                    'airline_id' => 1,
                    'stand_id' => $weightAppropriateStand->id,
                    'full_callsign' => '23451'
                ],
            ]
        );
        $aircraft = $this->createAircraft('BAW23451', 'EGLL', 'EGGD');
        $this->assertEquals($weightAppropriateStand->id, $this->allocator->allocate($aircraft));
    }

    public function testItAllocatesAStandInAerodromeReferenceAscendingOrder()
    {
        Aircraft::where('code', 'B738')->update(['aerodrome_reference_code' => 'B']);
        $weightAppropriateStand = Stand::create(
            [
                'airfield_id' => 1,
                'identifier' => '502',
                'latitude' => 54.65875500,
                'longitude' => -6.22258694,
                'aerodrome_reference_code' => 'B',
            ]
        );
        DB::table('airline_stand')->insert(
            [
                [
                    'airline_id' => 1,
                    'stand_id' => 1,
                    'full_callsign' => null
                ],
                [
                    'airline_id' => 1,
                    'stand_id' => 2,
                    'full_callsign' => '23451'
                ],
                [
                    'airline_id' => 1,
                    'stand_id' => 3,
                    'full_callsign' => null
                ],
                [
                    'airline_id' => 2,
                    'stand_id' => 1,
                    'full_callsign' => '23451'
                ],
                [
                    'airline_id' => 1,
                    'stand_id' => $weightAppropriateStand->id,
                    'full_callsign' => '23451'
                ],
            ]
        );
        $aircraft = $this->createAircraft('BAW23451', 'EGLL', 'EGGD');
        $this->assertEquals($weightAppropriateStand->id, $this->allocator->allocate($aircraft));
    }

    public function testItDoesntAllocatePartialMatches()
    {
        DB::table('airline_stand')->insert(
            [
                [
                    'airline_id' => 1,
                    'stand_id' => 2,
                    'full_callsign' => null
                ],
                [
                    'airline_id' => 1,
                    'stand_id' => 1,
                    'full_callsign' => '2'
                ],
            ]
        );
        $aircraft = $this->createAircraft('BAW23451', 'EGLL', 'EGGD');
        $this->assertNull($this->allocator->allocate($aircraft));
    }

    public function testItDoesntAllocateOccupiedStands()
    {
        DB::table('airline_stand')->insert(
            [
                [
                    'airline_id' => 1,
                    'stand_id' => 1,
                    'full_callsign' => '23451'
                ],
                [
                    'airline_id' => 1,
                    'stand_id' => 2,
                    'full_callsign' => '23451'
                ],
                [
                    'airline_id' => 1,
                    'stand_id' => 3,
                    'full_callsign' => null
                ],
                [
                    'airline_id' => 2,
                    'stand_id' => 1,
                    'full_callsign' => '23451'
                ],
            ]
        );

        $occupier = $this->createAircraft('EZY7823', 'EGLL', 'EGGD');
        $occupier->occupiedStand()->sync([1]);
        $aircraft = $this->createAircraft('BAW23451', 'EGLL', 'EGGD');
        $this->assertEquals(2, $this->allocator->allocate($aircraft));
    }

    public function testItDoesntAllocateAStandWithNoCallsign()
    {
        DB::table('airline_stand')->insert(
            [
                [
                    'airline_id' => 1,
                    'stand_id' => 2,
                    'full_callsign' => null
                ],
                [
                    'airline_id' => 1,
                    'stand_id' => 1,
                    'full_callsign' => null
                ],
                [
                    'airline_id' => 2,
                    'stand_id' => 1,
                    'full_callsign' => '23451'
                ],
            ]
        );
        $aircraft = $this->createAircraft('BAW23451', 'EGLL', 'EGGD');
        $this->assertNull($this->allocator->allocate($aircraft));
    }

    public function testItDoesntAllocateAtTheWrongAirfield()
    {
        DB::table('airline_stand')->insert(
            [
                [
                    'airline_id' => 1,
                    'stand_id' => 3,
                    'full_callsign' => '23451'
                ],
                [
                    'airline_id' => 2,
                    'stand_id' => 1,
                    'full_callsign' => '23451'
                ],
            ]
        );
        $aircraft = $this->createAircraft('BAW23451', 'EGLL', 'EGGD');
        $this->assertNull($this->allocator->allocate($aircraft));
    }

    public function testItDoesntAllocateForTheWrongCallsign()
    {
        DB::table('airline_stand')->insert(
            [
                [
                    'airline_id' => 2,
                    'stand_id' => 1,
                    'full_callsign' => '5'
                ],
            ]
        );
        $aircraft = $this->createAircraft('BAW23451', 'EGLL', 'EGGD');
        $this->assertNull($this->allocator->allocate($aircraft));
    }

    public function testItDoesntAllocateUnavailableStands()
    {
        DB::table('airline_stand')->insert(
            [
                [
                    'airline_id' => 1,
                    'stand_id' => 1,
                    'full_callsign' => '23451'
                ],
                [
                    'airline_id' => 1,
                    'stand_id' => 2,
                    'full_callsign' => '23451'
                ],
            ]
        );
        NetworkAircraft::find('BAW123')->occupiedStand()->sync([1]);

        $aircraft = $this->createAircraft('BAW23451', 'EGLL', 'EGGD');
        $this->assertEquals(2, $this->allocator->allocate($aircraft));
    }

    public function testItDoesntAllocateNonExistentAirlines()
    {
        DB::table('airline_stand')->insert(
            [
                [
                    'airline_id' => 1,
                    'stand_id' => 3,
                    'full_callsign' => '23451'
                ],
                [
                    'airline_id' => 2,
                    'stand_id' => 1,
                    'full_callsign' => '23451'
                ],
            ]
        );
        $aircraft = $this->createAircraft('***1234', 'EGLL', 'EGGD');
        $this->assertNull($this->allocator->allocate($aircraft));
    }

    private function createAircraft(
        string $callsign,
        string $arrivalAirport,
        string $departureAirport,
        string $aircraftType = 'B738'
    ): NetworkAircraft {
        return NetworkAircraft::create(
            [
                'callsign' => $callsign,
                'cid' => 1234,
                'planned_aircraft' => $aircraftType,
                'planned_aircraft_short' => $aircraftType,
                'planned_destairport' => $arrivalAirport,
                'planned_depairport' => $departureAirport,
                'aircraft_id' => $aircraftType === 'B738' ? 1 : null,
                'airline_id' => match ($callsign) {
                    'BAW23451' => 1,
                    'EZY7823' => Airline::where('icao_code', 'EZY')->first()->id,
                    default => null,
                },
            ]
        );
    }
}
