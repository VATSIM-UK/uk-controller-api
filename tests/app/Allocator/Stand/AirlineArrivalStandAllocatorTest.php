<?php

namespace App\Allocator\Stand;

use App\BaseFunctionalTestCase;
use App\Models\Aircraft\WakeCategory;
use App\Models\Stand\Stand;
use App\Models\Vatsim\NetworkAircraft;
use Illuminate\Support\Facades\DB;
use util\Traits\WithWakeCategories;

class AirlineArrivalStandAllocatorTest extends BaseFunctionalTestCase
{
    use WithWakeCategories;

    private AirlineArrivalStandAllocator $allocator;

    public function setUp(): void
    {
        parent::setUp();
        $this->allocator = $this->app->make(AirlineArrivalStandAllocator::class);
    }

    public function testItAllocatesAStandForTheAirline()
    {
        DB::table('airline_stand')->insert(
            [
                [
                    'airline_id' => 1,
                    'stand_id' => 2,
                    'destination' => null
                ],
                [
                    'airline_id' => 1,
                    'stand_id' => 1,
                    'destination' => null
                ],
                [
                    'airline_id' => 2,
                    'stand_id' => 1,
                    'destination' => 'EGGD'
                ],
            ]
        );
        $aircraft = $this->createAircraft('BAW23451', 'EGLL');
        $this->assertContains($this->allocator->allocate($aircraft), [1, 2]);
    }

    public function testItConsidersAirlinePreferences()
    {
        DB::table('airline_stand')->insert(
            [
                [
                    'airline_id' => 1,
                    'stand_id' => 2,
                    'destination' => null,
                    'priority' => 100,
                ],
                [
                    'airline_id' => 1,
                    'stand_id' => 1,
                    'destination' => null,
                    'priority' => 1,
                ],
                [
                    'airline_id' => 2,
                    'stand_id' => 1,
                    'destination' => null,
                    'priority' => 100,
                ],
            ]
        );
        $aircraft = $this->createAircraft('BAW23451', 'EGLL');
        $this->assertEquals($this->allocator->allocate($aircraft), 1);
    }

    public function testItAssignsStandsWithSpecificDestinations()
    {
        DB::table('airline_stand')->insert(
            [
                [
                    'airline_id' => 1,
                    'stand_id' => 1,
                    'destination' => 'EGGD'
                ],
            ]
        );
        $aircraft = $this->createAircraft('BAW23451', 'EGLL');
        $this->assertEquals(1, $this->allocator->allocate($aircraft));
    }

    public function testItAPrefersStandsWithNoSpecificDestinations()
    {
        DB::table('airline_stand')->insert(
            [
                [
                    'airline_id' => 1,
                    'stand_id' => 1,
                    'destination' => 'EGGD',
                ],
                [
                    'airline_id' => 1,
                    'stand_id' => 2,
                    'destination' => null,
                ],
            ]
        );
        $aircraft = $this->createAircraft('BAW23451', 'EGLL');
        $this->assertEquals(2, $this->allocator->allocate($aircraft));
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
                'wake_category_id' => WakeCategory::where('code', 'UM')->first()->id
            ]
        );
        DB::table('airline_stand')->insert(
            [
                [
                    'airline_id' => 1,
                    'stand_id' => 2,
                    'destination' => null
                ],
                [
                    'airline_id' => 1,
                    'stand_id' => 1,
                    'destination' => null
                ],
                [
                    'airline_id' => 2,
                    'stand_id' => 1,
                    'destination' => 'EGGD'
                ],
                [
                    'airline_id' => 1,
                    'stand_id' => $weightAppropriateStand->id,
                    'destination' => null
                ],
            ]
        );
        $aircraft = $this->createAircraft('BAW23451', 'EGLL');
        $this->assertEquals($weightAppropriateStand->id, $this->allocator->allocate($aircraft));
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
                'wake_category_id' => WakeCategory::where('code', 'S')->first()->id
            ]
        );
        DB::table('airline_stand')->insert(
            [
                [
                    'airline_id' => 1,
                    'stand_id' => 2,
                    'destination' => null
                ],
                [
                    'airline_id' => 1,
                    'stand_id' => 1,
                    'destination' => null
                ],
                [
                    'airline_id' => 2,
                    'stand_id' => 1,
                    'destination' => 'EGGD'
                ],
                [
                    'airline_id' => 1,
                    'stand_id' => $weightAppropriateStand->id,
                    'destination' => null
                ],
            ]
        );
        $aircraft = $this->createAircraft('BAW23451', 'EGLL');
        $this->assertEquals($weightAppropriateStand->id, $this->allocator->allocate($aircraft));
    }

    public function testItDoesntAllocateOccupiedStands()
    {
        DB::table('airline_stand')->insert(
            [
                [
                    'airline_id' => 1,
                    'stand_id' => 2,
                    'destination' => null
                ],
                [
                    'airline_id' => 1,
                    'stand_id' => 1,
                    'destination' => null
                ],
                [
                    'airline_id' => 2,
                    'stand_id' => 1,
                    'destination' => 'EGGD'
                ],
            ]
        );

        $occupier = $this->createAircraft('EZY7823', 'EGLL');
        $occupier->occupiedStand()->sync([2]);
        $aircraft = $this->createAircraft('BAW23451', 'EGLL');

        $this->assertEquals(1, $this->allocator->allocate($aircraft));
    }

    public function testItDoesntAllocateAtTheWrongAirfield()
    {
        DB::table('airline_stand')->insert(
            [
                [
                    'airline_id' => 1,
                    'stand_id' => 3,
                    'destination' => null
                ],
                [
                    'airline_id' => 2,
                    'stand_id' => 1,
                    'destination' => 'EGGD'
                ],
            ]
        );
        $aircraft = $this->createAircraft('BAW23451', 'EGLL');
        $this->assertNull($this->allocator->allocate($aircraft));
    }

    public function testItDoesntAllocateForTheWrongAirline()
    {
        DB::table('airline_stand')->insert(
            [
                [
                    'airline_id' => 2,
                    'stand_id' => 1,
                    'destination' => null
                ],
            ]
        );
        $aircraft = $this->createAircraft('BAW23451', 'EGLL');
        $this->assertNull($this->allocator->allocate($aircraft));
    }

    public function testItDoesntAllocateUnavailableStands()
    {
        DB::table('airline_stand')->insert(
            [
                [
                    'airline_id' => 1,
                    'stand_id' => 1,
                    'destination' => null
                ],
                [
                    'airline_id' => 1,
                    'stand_id' => 2,
                    'destination' => null
                ],
            ]
        );
        NetworkAircraft::find('BAW123')->occupiedStand()->sync([1]);

        $aircraft = $this->createAircraft('BAW23451', 'EGLL');
        $this->assertEquals(2, $this->allocator->allocate($aircraft));
    }

    public function testItDoesntAllocateNonExistentAirlines()
    {
        DB::table('airline_stand')->insert(
            [
                [
                    'airline_id' => 1,
                    'stand_id' => 3,
                    'destination' => null
                ],
                [
                    'airline_id' => 2,
                    'stand_id' => 1,
                    'destination' => 'EGGD'
                ],
            ]
        );
        $aircraft = $this->createAircraft('***1234', 'EGLL');
        $this->assertNull($this->allocator->allocate($aircraft));
    }

    private function createAircraft(string $callsign, string $arrivalAirport): NetworkAircraft
    {
        return NetworkAircraft::create(
            [
                'callsign' => $callsign,
                'planned_aircraft' => 'B738',
                'planned_aircraft_short' => 'B738',
                'planned_destairport' => $arrivalAirport]
        );
    }
}
