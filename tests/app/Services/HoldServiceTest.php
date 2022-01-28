<?php

namespace App\Services;

use App\BaseFunctionalTestCase;
use App\Events\Hold\AircraftEnteredHoldingArea;
use App\Events\Hold\AircraftExitedHoldingArea;
use App\Events\HoldUnassignedEvent;
use App\Models\Hold\AssignedHold;
use App\Models\Hold\Hold;
use App\Models\Navigation\Navaid;
use App\Models\Vatsim\NetworkAircraft;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;

class HoldServiceTest extends BaseFunctionalTestCase
{
    private HoldService $holdService;

    public function setUp(): void
    {
        parent::setUp();
        $this->holdService = $this->app->make(HoldService::class);
        Event::fake();
        AssignedHold::where('callsign', '<>', 'BAW123')->delete();
        NetworkAircraft::where('callsign', '<>', 'BAW123')->delete();
        Carbon::setTestNow(Carbon::now()->startOfSecond());
    }

    public function testItConstructs()
    {
        $this->assertInstanceOf(HoldService::class, $this->holdService);
    }

    public function testItReturnsAllHolds()
    {
        Hold::find(1)->deemedSeparatedHolds()->sync(
            [
                2 => ['vsl_insert_distance' => 5],
                3 => ['vsl_insert_distance' => 7]
            ]
        );
        $expected = [
            [
                'id' => 1,
                'fix' => 'WILLO',
                'inbound_heading' => 285,
                'minimum_altitude' => 7000,
                'maximum_altitude' => 15000,
                'turn_direction' => 'left',
                'description' => 'WILLO',
                'restrictions' => [
                    [
                        'foo' => 'bar',
                    ],
                ],
                'deemed_separated_holds' => [
                    [
                        'hold_id' => 2,
                        'vsl_insert_distance' => 5,
                    ],
                    [
                        'hold_id' => 3,
                        'vsl_insert_distance' => 7,
                    ],
                ],
            ],
            [
                'id' => 2,
                'fix' => 'TIMBA',
                'inbound_heading' => 309,
                'minimum_altitude' => 7000,
                'maximum_altitude' => 15000,
                'turn_direction' => 'right',
                'description' => 'TIMBA',
                'restrictions' => [],
                'deemed_separated_holds' => [],
            ],
            [
                'id' => 3,
                'fix' => 'MAY',
                'inbound_heading' => 90,
                'minimum_altitude' => 3000,
                'maximum_altitude' => 5000,
                'turn_direction' => 'right',
                'description' => 'Mayfield Low',
                'restrictions' => [],
                'deemed_separated_holds' => [],
            ],
        ];
        $actual = $this->holdService->getHolds();
        $this->assertEquals($expected, $actual);
    }

    public function testItRemovesStaleAssignmentIfAircraftOnGround()
    {
        $this->expectsEvents(HoldUnassignedEvent::class);
        NetworkAircraft::where('callsign', 'BAW123')->update(
            [
                'groundspeed' => 0,
                'altitude' => 999,
                'latitude' => 50.9850000,
                'longitude' => -0.1916667,
            ]
        );

        $this->holdService->removeStaleAssignments();
        $this->assertTrue(AssignedHold::where('callsign', 'BAW123')->doesntExist());
    }

    public function testItRemovesStaleAssignmentIfAreALongWayFromTheHold()
    {
        $this->expectsEvents(HoldUnassignedEvent::class);
        NetworkAircraft::where('callsign', 'BAW123')->update(
            [
                'groundspeed' => 123,
                'altitude' => 1000,
                'latitude' => 51.989700, // This is Barkway
                'longitude' => 0.061944,
            ]
        );

        $this->holdService->removeStaleAssignments();
        $this->assertTrue(AssignedHold::where('callsign', 'BAW123')->doesntExist());
    }

    public function testItDoesntRemoveStaleAssignmentsIfFlyingCloseToHold()
    {
        $this->doesntExpectEvents(HoldUnassignedEvent::class);
        NetworkAircraft::where('callsign', 'BAW123')->update(
            [
                'groundspeed' => 335,
                'altitude' => 7241,
                'latitude' => 50.9850000,
                'longitude' => -0.1916667,
            ]
        );

        $this->holdService->removeStaleAssignments();
        $this->assertTrue(AssignedHold::where('callsign', 'BAW123')->exists());
    }

    public function testItDoesNotAddProximityNavaidsIfOutOfRange()
    {
        Navaid::where('id', '<>', 1)->delete();
        NetworkAircraft::where('callsign', 'BAW123')->update(
            [
                'groundspeed' => 123,
                'altitude' => 1000,
                'latitude' => 51.989700, // This is Barkway
                'longitude' => 0.061944,
            ]
        );
        $this->holdService->checkAircraftHoldProximity();

        $this->assertDatabaseCount(
            'navaid_network_aircraft',
            0
        );
        Event::assertNotDispatched(AircraftEnteredHoldingArea::class);
        Event::assertNotDispatched(AircraftExitedHoldingArea::class);
    }

    public function testItAddsAircraftToProximityNavaids()
    {
        Navaid::where('id', '<>', 1)->delete();
        NetworkAircraft::where('callsign', 'BAW123')->update(
            [
                'groundspeed' => 335,
                'altitude' => 7241,
                'latitude' => 50.9850000,
                'longitude' => -0.1916667,
            ]
        );
        $this->holdService->checkAircraftHoldProximity();

        $this->assertDatabaseCount(
            'navaid_network_aircraft',
            1
        );
        $this->assertDatabaseHas(
            'navaid_network_aircraft',
            [
                'callsign' => 'BAW123',
                'navaid_id' => 1,
                'entered_at' => Carbon::now()->utc()->toDateTimeString(),
            ]
        );

        Event::assertDispatched(
            AircraftEnteredHoldingArea::class,
            fn(AircraftEnteredHoldingArea $event) => $event->broadcastWith() == [
                    'navaid_id' => 1,
                    'callsign' => 'BAW123',
                    'entered_at' => Carbon::now()
                ]
        );

        Event::assertNotDispatched(AircraftExitedHoldingArea::class);
    }

    public function testItDoesntRemoveAircraftStillInProximity()
    {
        Navaid::where('id', '<>', 1)->delete();
        NetworkAircraft::where('callsign', 'BAW123')->update(
            [
                'groundspeed' => 335,
                'altitude' => 7241,
                'latitude' => 50.9850000,
                'longitude' => -0.1916667,
            ]
        );
        DB::table('navaid_network_aircraft')->insert(
            ['navaid_id' => 1, 'callsign' => 'BAW123', 'entered_at' => Carbon::now()->subHour()->toDateTimeString()]
        );

        $this->holdService->checkAircraftHoldProximity();

        $this->assertDatabaseCount(
            'navaid_network_aircraft',
            1
        );
        $this->assertDatabaseHas(
            'navaid_network_aircraft',
            [
                'callsign' => 'BAW123',
                'navaid_id' => 1,
                'entered_at' => Carbon::now()->subHour()->toDateTimeString()
            ]
        );

        Event::assertNotDispatched(AircraftEnteredHoldingArea::class);
        Event::assertNotDispatched(AircraftExitedHoldingArea::class);
    }

    public function testItRemovesAircraftFromProximityThatIfNoLongerClose()
    {
        Navaid::where('id', '<>', 1)->delete();
        NetworkAircraft::where('callsign', 'BAW123')->update(
            [
                'groundspeed' => 123,
                'altitude' => 1000,
                'latitude' => 51.989700, // This is Barkway
                'longitude' => 0.061944,
            ]
        );
        DB::table('navaid_network_aircraft')->insert(
            ['navaid_id' => 1, 'callsign' => 'BAW123', 'entered_at' => Carbon::now()->toDateTimeString()]
        );

        $this->holdService->checkAircraftHoldProximity();

        $this->assertDatabaseCount(
            'navaid_network_aircraft',
            0
        );

        Event::assertNotDispatched(AircraftEnteredHoldingArea::class);
        Event::assertDispatched(
            AircraftExitedHoldingArea::class,
            fn(AircraftExitedHoldingArea $event) => $event->broadcastWith() == [
                    'navaid_id' => 1,
                    'callsign' => 'BAW123',
                ]
        );
    }
}
