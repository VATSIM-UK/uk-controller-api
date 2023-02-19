<?php

namespace App\Http\Controllers;

use App\BaseApiTestCase;
use App\Events\EnrouteReleaseEvent;
use App\Models\Release\Enroute\EnrouteReleaseType;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use PHPUnit\Framework\Attributes\DataProvider;

class ReleaseControllerTest extends BaseApiTestCase
{
    public function setUp(): void
    {
        parent::setUp();
        Carbon::setTestNow(Carbon::now());
        Event::fake();
    }

    public function testItReturnsEnrouteReleaseTypeDependency()
    {
        DB::table('enroute_release_types')->delete();
        EnrouteReleaseType::create(
            [
                'tag_string' => 'foo',
                'description' => 'foo description'
            ]
        );
        EnrouteReleaseType::create(
            [
                'tag_string' => 'bar',
                'description' => 'bar description'
            ]
        );

        $this->makeUnauthenticatedApiRequest(self::METHOD_GET, 'release/enroute/types')
            ->assertStatus(200)
            ->assertJson(
                [
                    [
                        'tag_string' => 'foo',
                        'description' => 'foo description'
                    ],
                    [
                        'tag_string' => 'bar',
                        'description' => 'bar description'
                    ],
                ]
            );
    }

    public function testItCreatesAReleaseWithReleasePoint()
    {
        $this->makeAuthenticatedApiRequest(
            self::METHOD_POST,
            'release/enroute',
            [
                'callsign' => 'BAW123',
                'type' => 1,
                'initiating_controller' => 'LON_S_CTR',
                'target_controller' => 'LON_C_CTR',
                'release_point' => 'LAM'
            ]
        )
            ->assertStatus(201);

        Event::assertDispatched(EnrouteReleaseEvent::class);

        $this->assertDatabaseHas(
            'enroute_releases',
            [
                'callsign' => 'BAW123',
                'enroute_release_type_id' => 1,
                'initiating_controller' => 'LON_S_CTR',
                'target_controller' => 'LON_C_CTR',
                'release_point' => 'LAM',
                'user_id' => self::ACTIVE_USER_CID,
                'released_at' => Carbon::now(),
            ]
        );
    }

    public function testItCreatesAReleaseWithReleasePointMaxLength()
    {
        $this->makeAuthenticatedApiRequest(
            self::METHOD_POST,
            'release/enroute',
            [
                'callsign' => 'BAW123',
                'type' => 1,
                'initiating_controller' => 'LON_S_CTR',
                'target_controller' => 'LON_C_CTR',
                'release_point' => '123456789012345'
            ]
        )
            ->assertStatus(201);

        Event::assertDispatched(EnrouteReleaseEvent::class);

        $this->assertDatabaseHas(
            'enroute_releases',
            [
                'callsign' => 'BAW123',
                'enroute_release_type_id' => 1,
                'initiating_controller' => 'LON_S_CTR',
                'target_controller' => 'LON_C_CTR',
                'release_point' => '123456789012345',
                'user_id' => self::ACTIVE_USER_CID,
                'released_at' => Carbon::now(),
            ]
        );
    }

    public function testItCreatesAReleaseWithNoReleasePoint()
    {
        $this->makeAuthenticatedApiRequest(
            self::METHOD_POST,
            'release/enroute',
            [
                'callsign' => 'BAW123',
                'type' => 1,
                'initiating_controller' => 'LON_S_CTR',
                'target_controller' => 'LON_C_CTR',
            ]
        )
            ->assertStatus(201);

        Event::assertDispatched(EnrouteReleaseEvent::class);

        $this->assertDatabaseHas(
            'enroute_releases',
            [
                'callsign' => 'BAW123',
                'enroute_release_type_id' => 1,
                'initiating_controller' => 'LON_S_CTR',
                'target_controller' => 'LON_C_CTR',
                'release_point' => null,
                'user_id' => self::ACTIVE_USER_CID,
                'released_at' => Carbon::now(),
            ]
        );
    }

    #[DataProvider('badDataProvider')]
    public function testItReturnsBadRequestOnBadData(array $data)
    {
        $this->makeAuthenticatedApiRequest(
            self::METHOD_POST,
            'release/enroute',
            $data,
        )
            ->assertStatus(400);
        Event::assertNotDispatched(EnrouteReleaseEvent::class);

        $this->assertDatabaseMissing(
            'enroute_releases',
            [
                'callsign' => 'BAW123',
            ]
        );
    }


    public static function badDataProvider(): array
    {
        return [
            [
                [
                    'callsign' => 'ASDASDSADSADSADASDSADSA',
                    'type' => 1,
                    'initiating_controller' => 'LON_S_CTR',
                    'target_controller' => 'LON_C_CTR',
                ]
            ],
            // Bad callsign
            [
                [
                    'type' => 1,
                    'initiating_controller' => 'LON_S_CTR',
                    'target_controller' => 'LON_C_CTR',
                ]
            ],
            // No callsign
            [
                [
                    'callsign' => 'BAW123',
                    'initiating_controller' => 'LON_S_CTR',
                    'target_controller' => 'LON_C_CTR',
                ]
            ],
            //No type
            [
                [
                    'callsign' => 'BAW123',
                    'type' => 'abc',
                    'initiating_controller' => 'LON_S_CTR',
                    'target_controller' => 'LON_C_CTR',
                ]
            ],
            // Bad Type
            [
                [
                    'callsign' => 'BAW123',
                    'type' => 1,
                    'initiating_controller' => 123,
                    'target_controller' => 'LON_C_CTR',
                ]
            ],
            // Bad init controller
            [
                [
                    'callsign' => 'BAW123',
                    'type' => 1,
                    'target_controller' => 'LON_C_CTR',
                ]
            ],
            // Mo init controller
            [
                [
                    'callsign' => 'BAW123',
                    'type' => 1,
                    'initiating_controller' => 'LON_S_CTR',
                    'target_controller' => 123,
                ]
            ],
            // Bad rec controller
            [
                [
                    'callsign' => 'BAW123',
                    'type' => 1,
                    'target_controller' => 'LON_S_CTR',
                ]
            ],
            // No rec controller
            [
                [
                    'callsign' => 'BAW123',
                    'type' => 1,
                    'initiating_controller' => 'LON_S_CTR',
                    'target_controller' => 'LON_C_CTR',
                    'release_point' => null,
                ]
            ],
            // Bad release point
            [
                [
                    'callsign' => 'BAW123',
                    'type' => 1,
                    'initiating_controller' => 'LON_S_CTR',
                    'target_controller' => 'LON_C_CTR',
                    'release_point' => 123,
                ]
            ],
            // Bad release point
            [
                [
                    'callsign' => 'BAW123',
                    'type' => 1,
                    'initiating_controller' => 'LON_S_CTR',
                    'target_controller' => 'LON_C_CTR',
                    'release_point' => '1234567890123456',
                ]
            ], // Bad release point - too long
        ];
    }

    public function testItReturnsNotFoundOnBadReleaseType()
    {
        $this->makeAuthenticatedApiRequest(
            self::METHOD_POST,
            'release/enroute',
            [
                'callsign' => 'BAW123',
                'type' => -5,
                'initiating_controller' => 'LON_S_CTR',
                'target_controller' => 'LON_C_CTR',
            ]
        )
            ->assertStatus(404);
            
        Event::assertNotDispatched(EnrouteReleaseEvent::class);

        $this->assertDatabaseMissing(
            'enroute_releases',
            [
                'callsign' => 'BAW123',
            ]
        );
    }
}
