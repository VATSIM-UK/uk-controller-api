<?php

namespace App\Http\Controllers;

use App\BaseApiTestCase;
use App\Events\HoldAssignedEvent;
use App\Events\HoldUnassignedEvent;
use App\Services\HoldService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;

class HoldControllerTest extends BaseApiTestCase
{
    const HOLD_ASSIGNED_URI = 'hold/assigned';
    const HOLD_ASSIGNED_URI_AIRCRAFT = 'hold/assigned/BAW123';

    public function setUp(): void
    {
        parent::setUp();
        Event::fake();
    }

    public function testItConstructs()
    {
        $this->assertInstanceOf(HoldController::class, $this->app->make(HoldController::class));
    }

    public function testItReturns200OnHoldDataSuccess()
    {
        $this->makeUnauthenticatedApiRequest(self::METHOD_GET, 'hold')->assertStatus(200);
    }

    public function testItReturnsHoldData()
    {
        $expected = $this->app->make(HoldService::class)->getHolds();
        $this->makeUnauthenticatedApiRequest(self::METHOD_GET, 'hold')->assertJson($expected);
    }

    public function testItGetsAssignedHolds()
    {
        $expected = [
            [
                'callsign' => 'BAW123',
                'navaid' => 'WILLO',
            ],
            [
                'callsign' => 'BAW456',
                'navaid' => 'TIMBA',
            ],
        ];

        $this->makeUnauthenticatedApiRequest(self::METHOD_GET, self::HOLD_ASSIGNED_URI)
            ->assertStatus(200)
            ->assertJson($expected);
    }

    public function testItDeletesAssignedHolds()
    {
        $this->makeAuthenticatedApiRequest(self::METHOD_DELETE, self::HOLD_ASSIGNED_URI_AIRCRAFT)
            ->assertStatus(204);

        Event::assertDispatched(HoldUnassignedEvent::class);

        $this->assertDatabaseMissing(
            'assigned_holds',
            [
                'callsign' => 'BAW123'
            ]
        );
    }

    public function testItCanDeleteRepeatedly()
    {
        $this->makeAuthenticatedApiRequest(self::METHOD_DELETE, self::HOLD_ASSIGNED_URI_AIRCRAFT)
            ->assertStatus(204);

        $this->makeAuthenticatedApiRequest(self::METHOD_DELETE, self::HOLD_ASSIGNED_URI_AIRCRAFT)
            ->assertStatus(204);

        $this->makeAuthenticatedApiRequest(self::METHOD_DELETE, self::HOLD_ASSIGNED_URI_AIRCRAFT)
            ->assertStatus(204);

        Event::assertDispatched(HoldUnassignedEvent::class);
    }

    public function testItAssignsHoldsUnknownCallsign()
    {
        $data = [
            'callsign' => 'BAW898',
            'navaid' => 'MAY'
        ];

        $this->makeAuthenticatedApiRequest(self::METHOD_PUT, self::HOLD_ASSIGNED_URI, $data)
            ->assertStatus(201);

        Event::assertDispatched(HoldAssignedEvent::class);

        $this->assertDatabaseHas(
            'assigned_holds',
            [
                'callsign' => 'BAW898',
                'navaid_id' => 3,
            ]
        );
    }

    public function testItAssignsHoldsKnownCallsign()
    {
        $data = [
            'callsign' => 'BAW789',
            'navaid' => 'MAY'
        ];

        $this->makeAuthenticatedApiRequest(self::METHOD_PUT, self::HOLD_ASSIGNED_URI, $data)
            ->assertStatus(201);

        Event::assertDispatched(HoldAssignedEvent::class);

        $this->assertDatabaseHas(
            'assigned_holds',
            [
                'callsign' => 'BAW789',
                'navaid_id' => 3,
            ]
        );
    }

    public function testItUpdatesExistingHold()
    {
        $data = [
            'callsign' => 'BAW123',
            'navaid' => 'MAY'
        ];

        $this->makeAuthenticatedApiRequest(self::METHOD_PUT, self::HOLD_ASSIGNED_URI, $data)
            ->assertStatus(201);

        Event::assertDispatched(HoldAssignedEvent::class);

        $this->assertDatabaseHas(
            'assigned_holds',
            [
                'callsign' => 'BAW123',
                'navaid_id' => 3,
            ]
        );

        $this->assertDatabaseMissing(
            'assigned_holds',
            [
                'callsign' => 'BAW123',
                'navaid_id' => 1,
            ]
        );
    }

    public function testItRejectsAssignedHoldNavaidDoesntExist()
    {
        $data = [
            'callsign' => 'BAW123',
            'navaid' => 'NOTMAY'
        ];

        $this->makeAuthenticatedApiRequest(self::METHOD_PUT, self::HOLD_ASSIGNED_URI, $data)
            ->assertStatus(422);
        Event::assertNotDispatched(HoldAssignedEvent::class);
    }

    public function testItRejectsAssignedHoldInvalidNavaid()
    {
        $data = [
            'callsign' => 'BAW123',
            'navaid' => '123'
        ];

        $this->makeAuthenticatedApiRequest(self::METHOD_PUT, self::HOLD_ASSIGNED_URI, $data)
            ->assertStatus(400);
        Event::assertNotDispatched(HoldAssignedEvent::class);
    }

    public function testItRejectsAssignedHoldMissingNavaid()
    {
        $data = [
            'callsign' => 'BAW123',
        ];

        $this->makeAuthenticatedApiRequest(self::METHOD_PUT, self::HOLD_ASSIGNED_URI, $data)
            ->assertStatus(400);
        Event::assertNotDispatched(HoldAssignedEvent::class);
    }

    public function testItRejectsAssignedHoldInvalidCallsign()
    {
        $data = [
            'callsign' => '[][]}]',
            'navaid' => 'TIMBA'
        ];

        $this->makeAuthenticatedApiRequest(self::METHOD_PUT, self::HOLD_ASSIGNED_URI, $data)
            ->assertStatus(400);
        Event::assertNotDispatched(HoldAssignedEvent::class);
    }

    public function testItRejectsAssignedHoldMissingCallsign()
    {
        $data = [
            'navaid' => 'TIMBA'
        ];

        $this->makeAuthenticatedApiRequest(self::METHOD_PUT, self::HOLD_ASSIGNED_URI, $data)
            ->assertStatus(400);
        Event::assertNotDispatched(HoldAssignedEvent::class);
    }

    public function testItReturnsAircraftCurrentlyInProximityToHolds()
    {
        DB::table('navaid_network_aircraft')
            ->insert(
                [
                    [
                        'callsign' => 'BAW123',
                        'navaid_id' => 1,
                        'entered_at' => '2022-01-28 17:59:52',
                    ],
                    [
                        'callsign' => 'BAW123',
                        'navaid_id' => 2,
                        'entered_at' => '2022-01-28 17:59:53',
                    ],
                    [
                        'callsign' => 'BAW456',
                        'navaid_id' => 2,
                        'entered_at' => '2022-01-28 17:59:54',
                    ],
                ]
            );

        $expected = [
            [
                'callsign' => 'BAW123',
                'navaid_id' => 1,
                'entered_at' => '2022-01-28T17:59:52.000000Z',
            ],
            [
                'callsign' => 'BAW123',
                'navaid_id' => 2,
                'entered_at' => '2022-01-28T17:59:53.000000Z',
            ],
            [
                'callsign' => 'BAW456',
                'navaid_id' => 2,
                'entered_at' => '2022-01-28T17:59:54.000000Z',
            ],
        ];

        $this->makeUnauthenticatedApiRequest(self::METHOD_GET, 'hold/proximity')
            ->assertOk()
            ->assertExactJson($expected);
    }
}
