<?php

namespace App\Http\Controllers;

use App\BaseApiTestCase;
use App\Events\StandAssignedEvent;
use App\Events\StandUnassignedEvent;
use App\Models\Stand\Stand;
use App\Models\Stand\StandAssignment;
use App\Services\NetworkAircraftService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Event;
use PHPUnit\Framework\Attributes\DataProvider;

class StandControllerTest extends BaseApiTestCase
{
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
