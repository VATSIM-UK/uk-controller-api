<?php

namespace App\Http\Controllers;

use App\BaseApiTestCase;
use App\Events\StandAssignedEvent;
use App\Events\StandUnassignedEvent;
use App\Models\Stand\Stand;
use App\Models\Stand\StandAssignment;
use App\Models\User\Role;
use App\Models\User\RoleKeys;
use App\Services\NetworkAircraftService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Event;
use PHPUnit\Framework\Attributes\DataProvider;
use TestingUtils\Traits\WithSeedUsers;

class StandControllerTest extends BaseApiTestCase
{
    use WithSeedUsers;

    public function setUp(): void
    {
        parent::setUp();
        Event::fake();
    }

    public function testItReturnsStandDependency()
    {
        $expected = [
            'EGLL' => [
                [
                    'id' => 1,
                    'identifier' => '1L',
                ],
                [
                    'id' => 2,
                    'identifier' => '251',
                ],
            ],
            'EGBB' => [
                [
                    'id' => 3,
                    'identifier' => '32',
                ]
            ],
        ];

        $this->makeUnauthenticatedApiRequest(self::METHOD_GET, 'stand/dependency')
            ->assertJson($expected)
            ->assertStatus(200);
    }

    public function testStandDependencyIgnoresClosedStands()
    {
        Stand::where('identifier', '1L')
            ->airfield('EGLL')
            ->firstOrFail()
            ->close();

        $expected = [
            'EGLL' => [
                [
                    'id' => 2,
                    'identifier' => '251',
                ],
            ],
            'EGBB' => [
                [
                    'id' => 3,
                    'identifier' => '32',
                ]
            ],
        ];

        $this->makeUnauthenticatedApiRequest(self::METHOD_GET, 'stand/dependency')
            ->assertJson($expected)
            ->assertStatus(200);
    }

    public function testItReturnsAllStandAssignments()
    {
        StandAssignment::insert(
            [
                [
                    'callsign' => 'BAW123',
                    'stand_id' => 1,
                ],
                [
                    'callsign' => 'BAW456',
                    'stand_id' => 2,
                ],
            ]
        );

        $expected = [
            [
                'callsign' => 'BAW123',
                'stand_id' => 1,
            ],
            [
                'callsign' => 'BAW456',
                'stand_id' => 2,
            ],
        ];

        $this->makeUnauthenticatedApiRequest(self::METHOD_GET, 'stand/assignment')
            ->assertJson($expected)
            ->assertStatus(200);
    }

    #[DataProvider('badAssignmentDataProvider')]
    public function testItReturnsInvalidRequestOnBadStandAssignmentData(array $data)
    {
        $this->makeAuthenticatedApiRequest(self::METHOD_PUT, 'stand/assignment', $data)
            ->assertStatus(400);
    }

    public static function badAssignmentDataProvider(): array
    {
        return [
            [
                [
                    'callsign' => 'asdfdsdfdsfdsfdsfdsfsdfsd',
                    'stand_id' => 1
                ]
            ],
            // Invalid callsign
            [
                [
                    'callsign' => null,
                    'stand_id' => 1
                ]
            ],
            // Callsign null
            [
                [
                    'stand_id' => 1
                ]
            ],
            // Callsign missing
            [
                [
                    'callsign' => 'BAW123',
                    'stand_id' => 'asdas'
                ]
            ],
            // Invalid stand id
            [
                [
                    'callsign' => 'BAW123',
                ]
            ],
            // Stand id missing
            [
                [
                    'callsign' => 'BAW123',
                    'stand_id' => null
                ]
            ], // Stand id null
        ];
    }

    public function testItDoesStandAssignment()
    {
        $data = [
            'callsign' => 'BAW123',
            'stand_id' => 1
        ];
        $this->makeAuthenticatedApiRequest(self::METHOD_PUT, 'stand/assignment', $data)
            ->assertStatus(201);

        Event::assertDispatched(StandAssignedEvent::class);

        $this->assertDatabaseHas(
            'stand_assignments',
            [
                'callsign' => 'BAW123',
                'stand_id' => 1,
            ]
        );
    }

    public function testItReturnsNotFoundOnAssignmentIfStandDoesNotExist()
    {
        $data = [
            'callsign' => 'BAW123',
            'stand_id' => 55
        ];
        $this->makeAuthenticatedApiRequest(self::METHOD_PUT, 'stand/assignment', $data)
            ->assertStatus(404);
        Event::assertNotDispatched(StandAssignedEvent::class);
    }

    public function testItDeletesStandAssignments()
    {
        $this->addStandAssignment('BAW123', 1);
        $this->makeAuthenticatedApiRequest(self::METHOD_DELETE, 'stand/assignment/BAW123')
            ->assertStatus(204);

        Event::assertDispatched(StandUnassignedEvent::class);
    }

    public function testItDeletesStandAssignmentsIfNonePresent()
    {
        $this->makeAuthenticatedApiRequest(self::METHOD_DELETE, 'stand/assignment/BAW123')
            ->assertStatus(204);
        Event::assertNotDispatched(StandUnassignedEvent::class);
    }

    public function testItReturnsFreshStandStatuses()
    {
        Carbon::setTestNow(Carbon::now());
        // Expired cache
        Cache::put('STAND_STATUS_EGLL', ['foo'], Carbon::now()->subMinutes(5)->subSecond());

        $expected = [
            'stands' => [
                [
                    'identifier' => '1L',
                    'status' => 'available',
                    'latitude' => '51.47436111', // 501 at LL
                    'longitude' => '-0.48953611',
                    'type' => null,
                    'airlines' => [],
                    'aerodrome_reference_code' => 'C',
                    'max_aircraft' => [
                        'wingspan' => null,
                        'length' => null,
                    ],
                ],
                [
                    'identifier' => '251',
                    'status' => 'available',
                    'latitude' => '51.47187222', // 512 at LL
                    'longitude' => '-0.48601389',
                    'type' => null,
                    'airlines' => [],
                    'aerodrome_reference_code' => 'C',
                    'max_aircraft' => [
                        'wingspan' => null,
                        'length' => null,
                    ],
                ],
            ],
            'generated_at' => Carbon::now()->toIso8601String(),
            'refresh_interval_minutes' => 5,
            'refresh_at' => Carbon::now()->addMinutes(5)->toIso8601String(),
        ];
        $this->makeUnauthenticatedApiRequest(self::METHOD_GET, 'stand/status?airfield=EGLL')
            ->assertStatus(200)
            ->assertJson($expected);
    }

    public function testItCachesStandStatuses()
    {
        Carbon::setTestNow(Carbon::now());
        $this->makeUnauthenticatedApiRequest(self::METHOD_GET, 'stand/status?airfield=EGLL');

        $this->assertTrue(Cache::has('STAND_STATUS_EGLL'));
    }

    public function testItReturnsCachedStatuses()
    {
        Carbon::setTestNow(Carbon::now());
        Cache::put('STAND_STATUS_EGLL', ['foo'], Carbon::now()->addSeconds(5));

        $this->makeUnauthenticatedApiRequest(self::METHOD_GET, 'stand/status?airfield=EGLL')
            ->assertStatus(200)
            ->assertJson(['foo']);
    }

    public function testItReturnsNotFoundOnUnknownStandStatusAirfield()
    {
        $this->makeUnauthenticatedApiRequest(self::METHOD_GET, 'stand/status?airfield=XXXX')
            ->assertStatus(404);
    }

    public function testItReturnsNotFoundIfNoStandAssignmentForAircraft()
    {
        $this->makeUnauthenticatedApiRequest(self::METHOD_GET, 'stands/assignment/ABCD')
            ->assertStatus(404);
    }

    public function testItReturnsStandAssignmentForAircraft()
    {
        $expected = [
            'id' => 2,
            'identifier' => '251',
            'airfield' => 'EGLL',
        ];

        $this->addStandAssignment('BAW123', 2);
        $this->makeUnauthenticatedApiRequest(self::METHOD_GET, 'stand/assignment/BAW123')
            ->assertStatus(200)
            ->assertJson($expected);
    }


    public function testItUploadsStandReservationPlanForVaaRole()
    {
        $this->activeUser()->roles()->sync([Role::idFromKey(RoleKeys::VAA)]);

        $response = $this->makeAuthenticatedApiRequest(
            self::METHOD_POST,
            'stand/reservations/plan',
            [
                'name' => 'Test event',
                'contact_email' => 'event@example.com',
                'start' => '2024-08-11 09:00:00',
                'end' => '2024-08-11 18:00:00',
                'reservations' => [
                    [
                        'airfield' => 'EGLL',
                        'stand' => '1L',
                        'callsign' => 'SBI24',
                        'cid' => 1234567,
                        'origin' => 'UUEE',
                        'destination' => 'EGLL',
                    ],
                ],
            ]
        );

        $response->assertStatus(201)
            ->assertJsonStructure(['plan_id', 'status', 'approval_due_at'])
            ->assertJson(['status' => 'pending']);

        $this->assertDatabaseHas('stand_reservation_plans', [
            'name' => 'Test event',
            'contact_email' => 'event@example.com',
            'status' => 'pending',
        ]);
        $this->assertDatabaseCount('stand_reservations', 0);
    }

    public function testItRejectsStandReservationPlanUploadForNonVaaRole()
    {
        $this->activeUser()->roles()->sync([]);

        $this->makeAuthenticatedApiRequest(
            self::METHOD_POST,
            'stand/reservations/plan',
            [
                'reservations' => [],
            ]
        )->assertStatus(403);

        $this->assertDatabaseCount('stand_reservations', 0);
    }

    public function testItAutoAssignsDepartureStand()
    {


        $this->makeAuthenticatedApiRequest(
            self::METHOD_POST,
            'stand/assignment/requestauto',
            [
                'callsign' => 'BAW9232',
                'assignment_type' => 'departure',
                'departure_airfield' => 'EGLL',
                'latitude' => 51.47187222,
                'longitude' => -0.48601389,
            ]
        )
            ->assertJson(['stand_id' => 2])
            ->assertStatus(201);

        $this->assertTrue(StandAssignment::where('callsign', 'BAW9232')->where('stand_id', 2)->exists());
        Event::assertDispatched(StandAssignedEvent::class);
    }

    public function testItReturns404NoDepartureStandAvailable()
    {


        $this->makeAuthenticatedApiRequest(
            self::METHOD_POST,
            'stand/assignment/requestauto',
            [
                'callsign' => 'BAW9232',
                'assignment_type' => 'departure',
                'departure_airfield' => 'EGLL',
                'latitude' => 0,
                'longitude' => -0.48601389,
            ]
        )
            ->assertNotFound();

        $this->assertFalse(StandAssignment::where('callsign', 'BAW9232')->exists());
    }

    public function testItAutoAssignsArrivalStand()
    {
        $response = $this->makeAuthenticatedApiRequest(
            self::METHOD_POST,
            'stand/assignment/requestauto',
            [
                'callsign' => 'BAW2932',
                'assignment_type' => 'arrival',
                'departure_airfield' => 'EGGW',
                'arrival_airfield' => 'EGLL',
                'aircraft_type' => 'B738',
            ]
        )
            ->assertJsonStructure(['stand_id'])
            ->assertStatus(201);

        $this->assertTrue(in_array($response->json('stand_id'), [1, 2]));
        $this->assertTrue(in_array(StandAssignment::where('callsign', 'BAW2932')->first()?->stand_id, [1, 2]));
        Event::assertDispatched(StandAssignedEvent::class);
    }

    public function testItAutoAssignsArrivalStandExistingAircraft()
    {
        NetworkAircraftService::createOrUpdateNetworkAircraft(
            'BAW2932',
            [
                'cid' => 123,
            ]
        );

        $response = $this->makeAuthenticatedApiRequest(
            self::METHOD_POST,
            'stand/assignment/requestauto',
            [
                'callsign' => 'BAW2932',
                'assignment_type' => 'arrival',
                'departure_airfield' => 'EGGW',
                'arrival_airfield' => 'EGLL',
                'aircraft_type' => 'B738',
            ]
        )
            ->assertJsonStructure(['stand_id'])
            ->assertStatus(201);

        $this->assertTrue(in_array($response->json('stand_id'), [1, 2]));
        $this->assertTrue(in_array(StandAssignment::where('callsign', 'BAW2932')->first()?->stand_id, [1, 2]));
        Event::assertDispatched(StandAssignedEvent::class);
    }


    public function testItAutoAssignsArrivalStandExistingAircraftUnknownAirline()
    {
        NetworkAircraftService::createOrUpdateNetworkAircraft(
            'BAW2932',
            [
                'cid' => 123,
            ]
        );

        $response = $this->makeAuthenticatedApiRequest(
            self::METHOD_POST,
            'stand/assignment/requestauto',
            [
                'callsign' => 'XYZ2932',
                'assignment_type' => 'arrival',
                'departure_airfield' => 'EGGW',
                'arrival_airfield' => 'EGLL',
                'aircraft_type' => 'B738',
            ]
        )
            ->assertJsonStructure(['stand_id'])
            ->assertStatus(201);

        $this->assertTrue(in_array($response->json('stand_id'), [1, 2]));
        $this->assertTrue(in_array(StandAssignment::where('callsign', 'XYZ2932')->first()?->stand_id, [1, 2]));
        Event::assertDispatched(StandAssignedEvent::class);
    }

    public function testItReturns404ForAutoAssignArrivalStandNotAvailable()
    {
        NetworkAircraftService::createOrUpdateNetworkAircraft(
            'BAW2932',
            [
                'cid' => 123,
            ]
        );

        $response = $this->makeAuthenticatedApiRequest(
            self::METHOD_POST,
            'stand/assignment/requestauto',
            [
                'callsign' => 'XYZ2932',
                'assignment_type' => 'arrival',
                'departure_airfield' => 'EGGW',
                'arrival_airfield' => 'EGXY',
                'aircraft_type' => 'B738',
            ]
        )
        ->assertNotFound();

        $this->assertFalse(StandAssignment::where('callsign', 'XYZ2932')->exists());
    }

    #[DataProvider('badAutoAssignmentDataProvider')]
    public function testItReturnsBadRequestForAutoAssignmentRequestOnBadData(array $data)
    {
        $this->makeAuthenticatedApiRequest(self::METHOD_POST, 'stand/assignment/requestauto', $data)
            ->assertUnprocessable();
    }

    public static function badAutoAssignmentDataProvider(): array
    {
        return [
            'Missing assignment type' => [
                [
                    'callsign' => 'BAW123',
                    'departure_airfield' => 'EGLL',
                    'latitude' => 51.47187222,
                    'longitude' => -0.48601389,
                ]
            ],
            'Unknown assignment type' => [
                [
                    'callsign' => 'BAW123',
                    'assignment_type' => 'foo',
                    'departure_airfield' => 'EGLL',
                    'latitude' => 51.47187222,
                    'longitude' => -0.48601389,
                ]
            ],
            'Departure no callsign' => [
                [
                    'assignment_type' => 'departure',
                    'departure_airfield' => 'EGLL',
                    'latitude' => 51.47187222,
                    'longitude' => -0.48601389,
                ]
            ],
            'Departure callsign not string' => [
                [
                    'callsign' => 123,
                    'assignment_type' => 'departure',
                    'departure_airfield' => 'EGLL',
                    'latitude' => 51.47187222,
                    'longitude' => -0.48601389,
                ]
            ],
            'Departure callsign not valid' => [
                [
                    'callsign' => 'BAW1234567890123456789',
                    'assignment_type' => 'departure',
                    'departure_airfield' => 'EGLL',
                    'latitude' => 51.47187222,
                    'longitude' => -0.48601389,
                ]
            ],
            'Departure no departure airfield' => [
                [
                    'callsign' => 'BAW123',
                    'assignment_type' => 'departure',
                    'latitude' => 51.47187222,
                    'longitude' => -0.48601389,
                ]
            ],
            'Departure departure airfield not string' => [
                [
                    'callsign' => 'BAW123',
                    'assignment_type' => 'departure',
                    'departure_airfield' => 123,
                    'latitude' => 51.47187222,
                    'longitude' => -0.48601389,
                ]
            ],
            'Departure departure airfield not valid' => [
                [
                    'callsign' => 'BAW123',
                    'assignment_type' => 'departure',
                    'departure_airfield' => 'XXXXY',
                    'latitude' => 51.47187222,
                    'longitude' => -0.48601389,
                ]
            ],
            'Departure no latitude' => [
                [
                    'callsign' => 'BAW123',
                    'assignment_type' => 'departure',
                    'departure_airfield' => 'EGLL',
                    'longitude' => -0.48601389,
                ]
            ],
            'Departure latitude not float' => [
                [
                    'callsign' => 'BAW123',
                    'assignment_type' => 'departure',
                    'departure_airfield' => 'EGLL',
                    'latitude' => 'foo',
                    'longitude' => -0.48601389,
                ]
            ],
            'Departure no longitude' => [
                [
                    'callsign' => 'BAW123',
                    'assignment_type' => 'departure',
                    'departure_airfield' => 'EGLL',
                    'latitude' => 51.47187222,
                ]
            ],
            'Departure longitude not float' => [
                [
                    'callsign' => 'BAW123',
                    'assignment_type' => 'departure',
                    'departure_airfield' => 'EGLL',
                    'latitude' => 51.47187222,
                    'longitude' => 'foo',
                ]
            ],
            'Arrival no callsign' => [
                [
                    'assignment_type' => 'arrival',
                    'departure_airfield' => 'EGLL',
                    'arrival_airfield' => 'EGGW',
                    'aircraft_type' => 'B738',
                ]
            ],
            'Arrival callsign not string' => [
                [
                    'callsign' => 123,
                    'assignment_type' => 'arrival',
                    'departure_airfield' => 'EGLL',
                    'arrival_airfield' => 'EGGW',
                    'aircraft_type' => 'B738',
                ]
            ],
            'Arrival callsign not valid' => [
                [
                    'callsign' => 'BAW1234567890123456789',
                    'assignment_type' => 'arrival',
                    'departure_airfield' => 'EGLL',
                    'arrival_airfield' => 'EGGW',
                    'aircraft_type' => 'B738',
                ]
            ],
            'Arrival no departure airfield' => [
                [
                    'callsign' => 'BAW123',
                    'assignment_type' => 'arrival',
                    'arrival_airfield' => 'EGGW',
                    'aircraft_type' => 'B738',
                ]
            ],
            'Arrival departure airfield not string' => [
                [
                    'callsign' => 'BAW123',
                    'assignment_type' => 'arrival',
                    'departure_airfield' => 123,
                    'arrival_airfield' => 'EGGW',
                    'aircraft_type' => 'B738',
                ]
            ],
            'Arrival departure airfield not valid' => [
                [
                    'callsign' => 'BAW123',
                    'assignment_type' => 'arrival',
                    'departure_airfield' => 'XXXXY',
                    'arrival_airfield' => 'EGGW',
                    'aircraft_type' => 'B738',
                ]
            ],
            'Arrival no arrival airfield' => [
                [
                    'callsign' => 'BAW123',
                    'assignment_type' => 'arrival',
                    'departure_airfield' => 'EGLL',
                    'aircraft_type' => 'B738',
                ]
            ],
            'Arrival arrival airfield not string' => [
                [
                    'callsign' => 'BAW123',
                    'assignment_type' => 'arrival',
                    'departure_airfield' => 'EGLL',
                    'arrival_airfield' => 123,
                    'aircraft_type' => 'B738',
                ]
            ],
            'Arrival arrival airfield not valid' => [
                [
                    'callsign' => 'BAW123',
                    'assignment_type' => 'arrival',
                    'departure_airfield' => 'EGLL',
                    'arrival_airfield' => 'XXXXY',
                    'aircraft_type' => 'B738',
                ]
            ],
            'Arrival no aircraft type' => [
                [
                    'callsign' => 'BAW123',
                    'assignment_type' => 'arrival',
                    'departure_airfield' => 'EGLL',
                    'arrival_airfield' => 'EGGW',
                ]
            ],
            'Arrival aircraft type not string' => [
                [
                    'callsign' => 'BAW123',
                    'assignment_type' => 'arrival',
                    'departure_airfield' => 'EGLL',
                    'arrival_airfield' => 'EGGW',
                    'aircraft_type' => 123,
                ]
            ],
            'Arrival aircraft type not valid' => [
                [
                    'callsign' => 'BAW123',
                    'assignment_type' => 'arrival',
                    'departure_airfield' => 'EGLL',
                    'arrival_airfield' => 'EGGW',
                    'aircraft_type' => 'XXXXY',
                ]
            ],
        ];
    }

    private function addStandAssignment(string $callsign, int $standId)
    {
        NetworkAircraftService::createPlaceholderAircraft($callsign);
        StandAssignment::create(
            [
                'callsign' => $callsign,
                'stand_id' => $standId,
            ]
        );
    }
}
