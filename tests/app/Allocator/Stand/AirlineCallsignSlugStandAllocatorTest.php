<?php

namespace App\Allocator\Stand;

use App\BaseFunctionalTestCase;
use App\Models\Aircraft\Aircraft;
use App\Models\Stand\Stand;
use App\Models\Vatsim\NetworkAircraft;
use Illuminate\Support\Facades\DB;

class AirlineCallsignSlugStandAllocatorTest extends BaseFunctionalTestCase
{
    public function setUp(): void
    {
        parent::setUp();
        $this->allocator = $this->app->make(AirlineCallsignSlugArrivalStandAllocator::class);
    }

    public function testItAllocatesAStandWithAFixedCallsignSlug()
    {
        DB::table('airline_stand')->insert(
            [
                [
                    'airline_id' => 1,
                    'stand_id' => 1,
                    'callsign_slug' => null
                ],
                [
                    'airline_id' => 1,
                    'stand_id' => 2,
                    'callsign_slug' => '23451'
                ],
                [
                    'airline_id' => 1,
                    'stand_id' => 3,
                    'callsign_slug' => null
                ],
                [
                    'airline_id' => 2,
                    'stand_id' => 1,
                    'callsign_slug' => '23451'
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
                    'callsign_slug' => '23451',
                    'priority' => 100,
                ],
                [
                    'airline_id' => 1,
                    'stand_id' => 2,
                    'callsign_slug' => '23451',
                    'priority' => 3,
                ],
                [
                    'airline_id' => 1,
                    'stand_id' => 3,
                    'callsign_slug' => '23451',
                    'priority' => 2,
                ],
                [
                    'airline_id' => 2,
                    'stand_id' => 1,
                    'callsign_slug' => '23451',
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
                    'callsign_slug' => null
                ],
                [
                    'airline_id' => 1,
                    'stand_id' => 2,
                    'callsign_slug' => '23451'
                ],
                [
                    'airline_id' => 1,
                    'stand_id' => 3,
                    'callsign_slug' => null
                ],
                [
                    'airline_id' => 2,
                    'stand_id' => 1,
                    'callsign_slug' => '23451'
                ],
                [
                    'airline_id' => 1,
                    'stand_id' => $weightAppropriateStand->id,
                    'callsign_slug' => '23451'
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
                    'callsign_slug' => null
                ],
                [
                    'airline_id' => 1,
                    'stand_id' => 2,
                    'callsign_slug' => '23451'
                ],
                [
                    'airline_id' => 1,
                    'stand_id' => 3,
                    'callsign_slug' => null
                ],
                [
                    'airline_id' => 2,
                    'stand_id' => 1,
                    'callsign_slug' => '23451'
                ],
                [
                    'airline_id' => 1,
                    'stand_id' => $weightAppropriateStand->id,
                    'callsign_slug' => '23451'
                ],
            ]
        );
        $aircraft = $this->createAircraft('BAW23451', 'EGLL', 'EGGD');
        $this->assertEquals($weightAppropriateStand->id, $this->allocator->allocate($aircraft));
    }

    public function testItAllocatesSingleCharacterMatches()
    {
        DB::table('airline_stand')->insert(
            [
                [
                    'airline_id' => 1,
                    'stand_id' => 2,
                    'callsign_slug' => null
                ],
                [
                    'airline_id' => 1,
                    'stand_id' => 1,
                    'callsign_slug' => '2'
                ],
            ]
        );
        $aircraft = $this->createAircraft('BAW23451', 'EGLL', 'EGGD');
        $this->assertEquals(1, $this->allocator->allocate($aircraft));
    }

    public function testItPrefersDoubleCharacterMatches()
    {
        $doubleCharacterStand = Stand::create(
            [
                'identifier' => '999',
                'airfield_id' => 1,
                'latitude' => 0,
                'longitude' => 0,
                'aerodrome_reference_code' => 'D'
            ]
        );

        DB::table('airline_stand')->insert(
            [
                [
                    'airline_id' => 1,
                    'stand_id' => 2,
                    'callsign_slug' => null
                ],
                [
                    'airline_id' => 1,
                    'stand_id' => 1,
                    'callsign_slug' => '2'
                ],
                [
                    'airline_id' => 1,
                    'stand_id' => $doubleCharacterStand->id,
                    'callsign_slug' => '23'
                ],
            ]
        );
        $aircraft = $this->createAircraft('BAW23451', 'EGLL', 'EGGD');
        $this->assertEquals($doubleCharacterStand->id, $this->allocator->allocate($aircraft));
    }

    public function testItPrefersTripleCharacterMatches()
    {
        $doubleCharacterStand = Stand::create(
            [
                'identifier' => '999',
                'airfield_id' => 1,
                'latitude' => 0,
                'longitude' => 0,
                'aerodrome_reference_code' => 'D'
            ]
        );

        $tripleCharacterStand = Stand::create(
            [
                'identifier' => '888',
                'airfield_id' => 1,
                'latitude' => 0,
                'longitude' => 0,
                'aerodrome_reference_code' => 'D'
            ]
        );

        DB::table('airline_stand')->insert(
            [
                [
                    'airline_id' => 1,
                    'stand_id' => 2,
                    'callsign_slug' => null
                ],
                [
                    'airline_id' => 1,
                    'stand_id' => 1,
                    'callsign_slug' => '2'
                ],
                [
                    'airline_id' => 1,
                    'stand_id' => $doubleCharacterStand->id,
                    'callsign_slug' => '23'
                ],
                [
                    'airline_id' => 1,
                    'stand_id' => $tripleCharacterStand->id,
                    'callsign_slug' => '234'
                ],
            ]
        );
        $aircraft = $this->createAircraft('BAW23451', 'EGLL', 'EGGD');
        $this->assertEquals($tripleCharacterStand->id, $this->allocator->allocate($aircraft));
    }

    public function testItPrefersFullMatches()
    {
        $doubleCharacterStand = Stand::create(
            [
                'identifier' => '999',
                'airfield_id' => 1,
                'latitude' => 0,
                'longitude' => 0,
                'aerodrome_reference_code' => 'D'
            ]
        );

        $tripleCharacterStand = Stand::create(
            [
                'identifier' => '888',
                'airfield_id' => 1,
                'latitude' => 0,
                'longitude' => 0,
                'aerodrome_reference_code' => 'D'
            ]
        );

        $fullMatchStand = Stand::create(
            [
                'identifier' => '777',
                'airfield_id' => 1,
                'latitude' => 0,
                'longitude' => 0,
                'aerodrome_reference_code' => 'D'
            ]
        );

        DB::table('airline_stand')->insert(
            [
                [
                    'airline_id' => 1,
                    'stand_id' => 2,
                    'callsign_slug' => null
                ],
                [
                    'airline_id' => 1,
                    'stand_id' => 1,
                    'callsign_slug' => '2'
                ],
                [
                    'airline_id' => 1,
                    'stand_id' => $doubleCharacterStand->id,
                    'callsign_slug' => '23'
                ],
                [
                    'airline_id' => 1,
                    'stand_id' => $tripleCharacterStand->id,
                    'callsign_slug' => '2345'
                ],
                [
                    'airline_id' => 1,
                    'stand_id' => $fullMatchStand->id,
                    'callsign_slug' => '23451'
                ],
            ]
        );
        $aircraft = $this->createAircraft('BAW23451', 'EGLL', 'EGGD');
        $this->assertEquals($fullMatchStand->id, $this->allocator->allocate($aircraft));
    }

    public function testItDoesntAllocateOccupiedStands()
    {
        DB::table('airline_stand')->insert(
            [
                [
                    'airline_id' => 1,
                    'stand_id' => 1,
                    'callsign_slug' => '23451'
                ],
                [
                    'airline_id' => 1,
                    'stand_id' => 2,
                    'callsign_slug' => '23451'
                ],
                [
                    'airline_id' => 1,
                    'stand_id' => 3,
                    'callsign_slug' => null
                ],
                [
                    'airline_id' => 2,
                    'stand_id' => 1,
                    'callsign_slug' => '23451'
                ],
            ]
        );

        $occupier = $this->createAircraft('EZY7823', 'EGLL', 'EGGD');
        $occupier->occupiedStand()->sync([1]);
        $aircraft = $this->createAircraft('BAW23451', 'EGLL', 'EGGD');
        $this->assertEquals(2, $this->allocator->allocate($aircraft));
    }

    public function testItDoesntAllocateAStandWithNoDestination()
    {
        DB::table('airline_stand')->insert(
            [
                [
                    'airline_id' => 1,
                    'stand_id' => 2,
                    'callsign_slug' => null
                ],
                [
                    'airline_id' => 1,
                    'stand_id' => 1,
                    'callsign_slug' => null
                ],
                [
                    'airline_id' => 2,
                    'stand_id' => 1,
                    'callsign_slug' => '23451'
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
                    'callsign_slug' => '23451'
                ],
                [
                    'airline_id' => 2,
                    'stand_id' => 1,
                    'callsign_slug' => '23451'
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
                    'callsign_slug' => '5'
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
                    'callsign_slug' => '23451'
                ],
                [
                    'airline_id' => 1,
                    'stand_id' => 2,
                    'callsign_slug' => '23451'
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
                    'callsign_slug' => '23451'
                ],
                [
                    'airline_id' => 2,
                    'stand_id' => 1,
                    'callsign_slug' => '23451'
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
