<?php

namespace App\Allocator\Stand;

use App\BaseFunctionalTestCase;
use App\Models\Aircraft\WakeCategory;
use App\Models\Airfield\Terminal;
use App\Models\Stand\Stand;
use App\Models\Vatsim\NetworkAircraft;
use Illuminate\Support\Facades\DB;
use util\Traits\WithWakeCategories;

class AirlineDestinationTerminalArrivalStandAllocatorTest extends BaseFunctionalTestCase
{
    use WithWakeCategories;

    private readonly AirlineDestinationTerminalArrivalStandAllocator $allocator;

    public function setUp(): void
    {
        parent::setUp();
        $this->allocator = $this->app->make(AirlineDestinationTerminalArrivalStandAllocator::class);
    }

    public function testItAllocatesAStandWithAFixedCallsignSlug()
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
                // Not picked, null destination
                [
                    'airline_id' => 1,
                    'terminal_id' => $terminal1->id,
                    'destination' => null
                ],
                // Will be picked, matches destination
                [
                    'airline_id' => 1,
                    'terminal_id' => $terminal1->id,
                    'destination' => 'EGGD'
                ],
                // Not picked, wrong airport
                [
                    'airline_id' => 1,
                    'terminal_id' => $terminal2->id,
                    'destination' => 'EGGD'
                ],
                // Not picked, wrong airline
                [
                    'airline_id' => 2,
                    'terminal_id' => $terminal1->id,
                    'destination' => 'EGGD'
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
                    'destination' => 'EGGD',
                    'priority' => 100,
                ],
                [
                    'airline_id' => 1,
                    'terminal_id' => $terminal2->id,
                    'destination' => 'EGGD',
                    'priority' => 3,
                ],
                [
                    'airline_id' => 1,
                    'terminal_id' => $terminal3->id,
                    'destination' => 'EGGD',
                    'priority' => 2,
                ],
                [
                    'airline_id' => 1,
                    'terminal_id' => $terminal3->id,
                    'destination' => 'EGGD',
                    'priority' => 2,
                ],
            ]
        );

        $aircraft = $this->createAircraft('BAW23451', 'EGLL', 'EGGD');
        $this->assertEquals($stand3->id, $this->allocator->allocate($aircraft));
    }

    public function testItAllocatesAStandWithAnAppropriateWeight()
    {
        $terminal = Terminal::factory()->create(['airfield_id' => 1]);
        $this->setWakeCategoryForAircraft('B738', 'UM');
        $weightAppropriateStand = Stand::create(
            [
                'airfield_id' => 1,
                'terminal_id' => $terminal->id,
                'identifier' => '502',
                'latitude' => 54.65875500,
                'longitude' => -6.22258694,
                'wake_category_id' => WakeCategory::where('code', 'UM')->first()->id,
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
                'wake_category_id' => WakeCategory::where('code', 'LM')->first()->id,
            ]
        );

        DB::table('airline_terminal')->insert(
            [
                [
                    'airline_id' => 1,
                    'terminal_id' => $terminal->id,
                    'destination' => 'EGGD'
                ],
            ]
        );
        $aircraft = $this->createAircraft('BAW23451', 'EGLL', 'EGGD');
        $this->assertEquals($weightAppropriateStand->id, $this->allocator->allocate($aircraft));
    }

    public function testItAllocatesAStandInWeightAscendingOrder()
    {
        $terminal = Terminal::factory()->create(['airfield_id' => 1]);
        $this->setWakeCategoryForAircraft('B738', 'S');
        $weightAppropriateStand = Stand::create(
            [
                'airfield_id' => 1,
                'terminal_id' => $terminal->id,
                'identifier' => '502',
                'latitude' => 54.65875500,
                'longitude' => -6.22258694,
                'wake_category_id' => WakeCategory::where('code', 'S')->first()->id,
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
                'wake_category_id' => WakeCategory::where('code', 'LM')->first()->id,
            ]
        );

        DB::table('airline_terminal')->insert(
            [
                [
                    'airline_id' => 1,
                    'terminal_id' => $terminal->id,
                    'destination' => 'EGGD'
                ],
            ]
        );

        $aircraft = $this->createAircraft('BAW23451', 'EGLL', 'EGGD');
        $this->assertEquals($weightAppropriateStand->id, $this->allocator->allocate($aircraft));
    }

    public function testItAllocatesSingleCharacterMatches()
    {
        $terminal1 = Terminal::factory()->create(['airfield_id' => 1]);
        $stand1 = Stand::factory()->withTerminal($terminal1)->create();

        // No slug
        $terminal2 = Terminal::factory()->create(['airfield_id' => 1]);
        Stand::factory()->withTerminal($terminal2)->create();

        DB::table('airline_terminal')->insert(
            [
                [
                    'airline_id' => 1,
                    'terminal_id' => $terminal2->id,
                    'destination' => null
                ],
                [
                    'airline_id' => 1,
                    'terminal_id' => $terminal1->id,
                    'destination' => 'E'
                ],
            ]
        );

        $aircraft = $this->createAircraft('BAW23451', 'EGLL', 'EGGD');
        $this->assertEquals($stand1->id, $this->allocator->allocate($aircraft));
    }

    public function testItPrefersDoubleCharacterMatches()
    {
        $terminal1 = Terminal::factory()->create(['airfield_id' => 1]);
        $stand1 = Stand::factory()->withTerminal($terminal1)->create();

        // No slug
        $terminal2 = Terminal::factory()->create(['airfield_id' => 1]);
        Stand::factory()->withTerminal($terminal2)->create();

        // Only single character
        $terminal3 = Terminal::factory()->create(['airfield_id' => 1]);
        Stand::factory()->withTerminal($terminal3)->create();

        DB::table('airline_terminal')->insert(
            [
                [
                    'airline_id' => 1,
                    'terminal_id' => $terminal2->id,
                    'destination' => null
                ],
                [
                    'airline_id' => 1,
                    'terminal_id' => $terminal3->id,
                    'destination' => 'E'
                ],
                [
                    'airline_id' => 1,
                    'terminal_id' => $terminal1->id,
                    'destination' => 'EG'
                ],
            ]
        );

        $aircraft = $this->createAircraft('BAW23451', 'EGLL', 'EGGD');
        $this->assertEquals($stand1->id, $this->allocator->allocate($aircraft));
    }

    public function testItPrefersTripleCharacterMatches()
    {
        $terminal1 = Terminal::factory()->create(['airfield_id' => 1]);
        $stand1 = Stand::factory()->withTerminal($terminal1)->create();

        // No slug
        $terminal2 = Terminal::factory()->create(['airfield_id' => 1]);
        Stand::factory()->withTerminal($terminal2)->create();

        // Only single character
        $terminal3 = Terminal::factory()->create(['airfield_id' => 1]);
        Stand::factory()->withTerminal($terminal3)->create();

        // Only double character
        $terminal4 = Terminal::factory()->create(['airfield_id' => 1]);
        Stand::factory()->withTerminal($terminal4)->create();

        DB::table('airline_terminal')->insert(
            [
                [
                    'airline_id' => 1,
                    'terminal_id' => $terminal2->id,
                    'destination' => null
                ],
                [
                    'airline_id' => 1,
                    'terminal_id' => $terminal3->id,
                    'destination' => 'E'
                ],
                [
                    'airline_id' => 1,
                    'terminal_id' => $terminal4->id,
                    'destination' => 'EG'
                ],
                [
                    'airline_id' => 1,
                    'terminal_id' => $terminal1->id,
                    'destination' => 'EGG'
                ],
            ]
        );

        $aircraft = $this->createAircraft('BAW23451', 'EGLL', 'EGGD');
        $this->assertEquals($stand1->id, $this->allocator->allocate($aircraft));
    }

    public function testItPrefersFullMatches()
    {
        $terminal1 = Terminal::factory()->create(['airfield_id' => 1]);
        $stand1 = Stand::factory()->withTerminal($terminal1)->create();

        // No slug
        $terminal2 = Terminal::factory()->create(['airfield_id' => 1]);
        Stand::factory()->withTerminal($terminal2)->create();

        // Only single character
        $terminal3 = Terminal::factory()->create(['airfield_id' => 1]);
        Stand::factory()->withTerminal($terminal3)->create();

        // Only double character
        $terminal4 = Terminal::factory()->create(['airfield_id' => 1]);
        Stand::factory()->withTerminal($terminal4)->create();

        // Only 4 chars
        $terminal5 = Terminal::factory()->create(['airfield_id' => 1]);
        Stand::factory()->withTerminal($terminal5)->create();

        DB::table('airline_terminal')->insert(
            [
                [
                    'airline_id' => 1,
                    'terminal_id' => $terminal2->id,
                    'destination' => null
                ],
                [
                    'airline_id' => 1,
                    'terminal_id' => $terminal3->id,
                    'destination' => 'E'
                ],
                [
                    'airline_id' => 1,
                    'terminal_id' => $terminal4->id,
                    'destination' => 'EG'
                ],
                [
                    'airline_id' => 1,
                    'terminal_id' => $terminal5->id,
                    'destination' => 'EGG'
                ],
                [
                    'airline_id' => 1,
                    'terminal_id' => $terminal1->id,
                    'destination' => 'EGGD'
                ],
            ]
        );

        $aircraft = $this->createAircraft('BAW23451', 'EGLL', 'EGGD');
        $this->assertEquals($stand1->id, $this->allocator->allocate($aircraft));
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
                    'destination' => 'EGGD'
                ],
            ]
        );

        $aircraft = $this->createAircraft('BAW23451', 'EGLL', 'EGGD');
        $this->assertEquals($stand1->id, $this->allocator->allocate($aircraft));
    }

    public function testItDoesntAllocateAStandWithNoSlugh()
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
                    'destination' => null
                ],
                [
                    'airline_id' => 2,
                    'terminal_id' => $terminal1->id,
                    'destination' => 'EGGD'
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
                    'destination' => 'EGGD'
                ],
            ]
        );

        $aircraft = $this->createAircraft('BAW23451', 'EGLL', 'EGGD');
        $this->assertNull($this->allocator->allocate($aircraft));
    }

    public function testItDoesntAllocateForTheWrongCallsign()
    {
        $terminal1 = Terminal::factory()->create(['airfield_id' => 1]);
        Stand::factory()->withTerminal($terminal1)->create();

        DB::table('airline_terminal')->insert(
            [
                [
                    'airline_id' => 1,
                    'terminal_id' => $terminal1->id,
                    'destination' => 'G'
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
                    'destination' => 'EG'
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
                    'destination' => 'EGGD'
                ],
                [
                    'airline_id' => 2,
                    'terminal_id' => $terminal1->id,
                    'destination' => 'EGGD'
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
                'planned_aircraft' => 'B738',
                'planned_aircraft_short' => 'B738',
                'planned_destairport' => $arrivalAirport,
                'planned_depairport' => $departureAirport,
            ]
        );
    }
}