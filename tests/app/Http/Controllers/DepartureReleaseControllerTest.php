<?php

namespace App\Http\Controllers;

use App\BaseApiTestCase;
use App\Models\Release\Departure\DepartureReleaseRequest;
use Carbon\Carbon;

class DepartureReleaseControllerTest extends BaseApiTestCase
{
    public function setUp(): void
    {
        parent::setUp();
        Carbon::setTestNow(Carbon::now());
        $this->withoutEvents();
    }

    public function testItRequestsARelease()
    {
        $requestData = [
            'callsign' => 'BAW123',
            'requesting_controller_id' => 1,
            'target_controller_ids' => [2, 3],
            'expires_in_seconds' => 125,
        ];

        $this->makeAuthenticatedApiRequest(self::METHOD_POST, 'departure/release/request', $requestData)
            ->assertCreated();

        $latestRelease = DepartureReleaseRequest::latest()->first()->id;

        $this->assertDatabaseHas(
            'departure_release_requests',
            [
                'callsign' => 'BAW123',
                'user_id' => self::ACTIVE_USER_CID,
                'controller_position_id' => 1,
                'expires_at' => Carbon::now()->addSeconds(125)
            ]
        );

        $this->assertDatabaseHas(
            'controller_position_departure_release_request',
            [
                'departure_release_request_id' => $latestRelease,
                'controller_position_id' => 2,
            ]
        );

        $this->assertDatabaseHas(
            'controller_position_departure_release_request',
            [
                'departure_release_request_id' => $latestRelease,
                'controller_position_id' => 3,
            ]
        );
    }

    public function badReleaseRequestProvider(): array
    {
        return [
            'Missing callsign' => [
                [
                    'requesting_controller_id' => 1,
                    'target_controller_ids' => [2, 3],
                    'expires_in_seconds' => 125,
                ]
            ],
            'Callsign not string' => [
                [
                    'callsign' => 123,
                    'requesting_controller_id' => 1,
                    'target_controller_ids' => [2, 3],
                    'expires_in_seconds' => 125,
                ]
            ],
            'Missing requesting controller_id' => [
                [
                    'callsign' => 'BAW123',
                    'target_controller_ids' => [2, 3],
                    'expires_in_seconds' => 125,
                ]
            ],
            'Requesting controller id not integer' => [
                [
                    'callsign' => 'BAW123',
                    'requesting_controller_id' => 'abc',
                    'target_controller_ids' => [2, 3],
                    'expires_in_seconds' => 125,
                ]
            ],
            'Requesting controller not a valid controller' => [
                [
                    'callsign' => 'BAW123',
                    'requesting_controller_id' => 12345,
                    'target_controller_ids' => [2, 3],
                    'expires_in_seconds' => 125,
                ]
            ],
            'Target controllers missing' => [
                [
                    'callsign' => 'BAW123',
                    'requesting_controller_id' => 1,
                    'expires_in_seconds' => 125,
                ]
            ],
            'Target controllers not an array' => [
                [
                    'callsign' => 'BAW123',
                    'requesting_controller_id' => 1,
                    'target_controller_ids' => 2,
                    'expires_in_seconds' => 125,
                ]
            ],
            'Target controller not a valid controller' => [
                [
                    'callsign' => 'BAW123',
                    'requesting_controller_id' => 1,
                    'target_controller_ids' => [2, 12345],
                    'expires_in_seconds' => 125,
                ]
            ],
            'Missing expires in seconds' => [
                [
                    'callsign' => 'BAW123',
                    'requesting_controller_id' => 1,
                    'target_controller_ids' => [2, 3],
                ]
            ],
            'Expires in seconds not integer' => [
                [
                    'callsign' => 'BAW123',
                    'requesting_controller_id' => 1,
                    'target_controller_ids' => [2, 3],
                    'expires_in_seconds' => 'abc',
                ]
            ],
        ];
    }

    /**
     * @dataProvider badReleaseRequestProvider
     */
    public function testItDoesntCreateAReleaseOnBadData(array $requestData)
    {
        $this->makeAuthenticatedApiRequest(self::METHOD_POST, 'departure/release/request', $requestData)
            ->assertStatus(422);
    }

    public function testReleaseRequestsRequireAuthorisation()
    {
        $this->makeUnauthenticatedApiRequest(self::METHOD_POST, 'departure/release/request', [])
            ->assertStatus(401);
    }
}
