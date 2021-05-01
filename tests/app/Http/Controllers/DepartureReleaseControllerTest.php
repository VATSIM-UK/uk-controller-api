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
        Carbon::setTestNow(Carbon::now()->startOfSecond());
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

        $response = $this->makeAuthenticatedApiRequest(self::METHOD_POST, 'departure/release/request', $requestData);
        $latestRelease = DepartureReleaseRequest::latest()->first()->id;

        $response->assertCreated()
            ->assertJson(['id' => $latestRelease]);

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
            'Requesting controller cannot request releases' => [
                [
                    'callsign' => 'BAW123',
                    'requesting_controller_id' => 4,
                    'target_controller_ids' => [2, 3],
                    'expires_in_seconds' => 125,
                ]
            ],
            'Requesting controller is requesting release of themselves' => [
                [
                    'callsign' => 'BAW123',
                    'requesting_controller_id' => 2,
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
            'Target controller cannot receive releases' => [
                [
                    'callsign' => 'BAW123',
                    'requesting_controller_id' => 1,
                    'target_controller_ids' => [2, 4],
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
            'Expires in seconds too low' => [
                [
                    'callsign' => 'BAW123',
                    'requesting_controller_id' => 1,
                    'target_controller_ids' => [2, 3],
                    'expires_in_seconds' => 0,
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

    public function testItApprovesARelease()
    {
        $request = DepartureReleaseRequest::create(
            [
                'callsign' => 'BAW123',
                'user_id' => self::ACTIVE_USER_CID,
                'controller_position_id' => 1,
                'expires_at' => Carbon::now()->addMinutes(2),
            ]
        );
        $request->controllerPositions()->sync([2, 3]);
        $route = sprintf('departure/release/request/%d/approve', $request->id);

        $approvalData = [
            'controller_position_id' => 2,
            'expires_in_seconds' => 10,
        ];

        $this->makeAuthenticatedApiRequest(self::METHOD_PUT, $route, $approvalData)
            ->assertOk();

        $this->assertDatabaseHas(
            'controller_position_departure_release_request',
            [
                'departure_release_request_id' => $request->id,
                'controller_position_id' => 2,
                'released_by' => self::ACTIVE_USER_CID,
                'release_expires_at' => Carbon::now()->addSeconds(10)->toDateTimeString()
            ]
        );
    }

    public function badApprovalDataProvider(): array
    {
        return [
            'Controller position id missing' => [
                [
                    'expires_in_seconds' => 10,
                ]
            ],
            'Controller position id not integer' => [
                [
                    'controller_position_id' => 'abc',
                    'expires_in_seconds' => 10,
                ]
            ],
            'Expires in seconds missing' => [
                [
                    'controller_position_id' => 2,
                ]
            ],
            'Expires in seconds not an integer' => [
                [
                    'controller_position_id' => 2,
                    'expires_in_seconds' => 'abc',
                ]
            ],
            'Expires in seconds too low' => [
                [
                    'controller_position_id' => 2,
                    'expires_in_seconds' => 0,
                ]
            ],
            'Controller position invalid' => [
                [
                    'controller_position_id' => 55,
                    'expires_in_seconds' => 10,
                ]
            ],
        ];
    }

    /**
     * @dataProvider badApprovalDataProvider
     */
    public function testReleasesCannotBeApprovedOnBadData(array $approvalData)
    {
        $request = DepartureReleaseRequest::create(
            [
                'callsign' => 'BAW123',
                'user_id' => self::ACTIVE_USER_CID,
                'controller_position_id' => 1,
                'expires_at' => Carbon::now()->addMinutes(2),
            ]
        );
        $request->controllerPositions()->sync([2, 3]);
        $route = sprintf('departure/release/request/%d/approve', $request->id);

        $this->makeAuthenticatedApiRequest(self::METHOD_PUT, $route, $approvalData)
            ->assertStatus(422);
    }

    public function testItReturnsNotFoundIfNoReleaseToApprove()
    {
        $this->makeAuthenticatedApiRequest(self::METHOD_PUT, 'departure/release/request/55/approve', [])
            ->assertNotFound();
    }

    public function testReleasesCannotBeApprovedByUnauthenticatedUsers()
    {
        $this->makeUnauthenticatedApiRequest(self::METHOD_PUT, 'departure/release/request/1/approve', [])
            ->assertUnauthorized();
    }

    public function testItRejectsARelease()
    {
        $request = DepartureReleaseRequest::create(
            [
                'callsign' => 'BAW123',
                'user_id' => self::ACTIVE_USER_CID,
                'controller_position_id' => 1,
                'expires_at' => Carbon::now()->addMinutes(2),
            ]
        );
        $request->controllerPositions()->sync([2, 3]);
        $route = sprintf('departure/release/request/%d/reject', $request->id);

        $rejectionData = [
            'controller_position_id' => 2,
        ];

        $this->makeAuthenticatedApiRequest(self::METHOD_PUT, $route, $rejectionData)
            ->assertOk();

        $this->assertDatabaseHas(
            'controller_position_departure_release_request',
            [
                'departure_release_request_id' => $request->id,
                'controller_position_id' => 2,
                'rejected_by' => self::ACTIVE_USER_CID,
                'rejected_at' => Carbon::now()->toDateTimeString()
            ]
        );
    }

    public function badRejectionDataProvider(): array
    {
        return [
            'Controller position id missing' => [
                [
                ]
            ],
            'Controller position id not integer' => [
                [
                    'controller_position_id' => 'abc',
                ]
            ],
            'Controller position invalid' => [
                [
                    'controller_position_id' => 55,
                ]
            ],
        ];
    }

    /**
     * @dataProvider badRejectionDataProvider
     */
    public function testReleasesCannotBeRejectedOnBadData(array $approvalData)
    {
        $request = DepartureReleaseRequest::create(
            [
                'callsign' => 'BAW123',
                'user_id' => self::ACTIVE_USER_CID,
                'controller_position_id' => 1,
                'expires_at' => Carbon::now()->addMinutes(2),
            ]
        );
        $request->controllerPositions()->sync([2, 3]);
        $route = sprintf('departure/release/request/%d/reject', $request->id);

        $this->makeAuthenticatedApiRequest(self::METHOD_PUT, $route, $approvalData)
            ->assertStatus(422);
    }

    public function testItReturnsNotFoundIfNoReleaseToReject()
    {
        $this->makeAuthenticatedApiRequest(self::METHOD_PUT, 'departure/release/request/55/reject', [])
            ->assertNotFound();
    }

    public function testReleasesCannotBeRejectedByUnauthenticatedUsers()
    {
        $this->makeUnauthenticatedApiRequest(self::METHOD_PUT, 'departure/release/request/1/reject', [])
            ->assertUnauthorized();
    }

    public function testItAcknowledgesARelease()
    {
        $request = DepartureReleaseRequest::create(
            [
                'callsign' => 'BAW123',
                'user_id' => self::ACTIVE_USER_CID,
                'controller_position_id' => 1,
                'expires_at' => Carbon::now()->addMinutes(2),
            ]
        );
        $request->controllerPositions()->sync([2, 3]);
        $route = sprintf('departure/release/request/%d/acknowledge', $request->id);

        $rejectionData = [
            'controller_position_id' => 2,
        ];

        $this->makeAuthenticatedApiRequest(self::METHOD_PUT, $route, $rejectionData)
            ->assertOk();

        $this->assertDatabaseHas(
            'controller_position_departure_release_request',
            [
                'departure_release_request_id' => $request->id,
                'controller_position_id' => 2,
                'acknowledged_by' => self::ACTIVE_USER_CID,
                'acknowledged_at' => Carbon::now()->toDateTimeString()
            ]
        );
    }

    public function badAcknowledgementDataProvider(): array
    {
        return [
            'Controller position id missing' => [
                [
                ]
            ],
            'Controller position id not integer' => [
                [
                    'controller_position_id' => 'abc',
                ]
            ],
            'Controller position invalid' => [
                [
                    'controller_position_id' => 55,
                ]
            ],
        ];
    }

    /**
     * @dataProvider badAcknowledgementDataProvider
     */
    public function testReleasesCannotBeAcknowledgedOnBadData(array $approvalData)
    {
        $request = DepartureReleaseRequest::create(
            [
                'callsign' => 'BAW123',
                'user_id' => self::ACTIVE_USER_CID,
                'controller_position_id' => 1,
                'expires_at' => Carbon::now()->addMinutes(2),
            ]
        );
        $request->controllerPositions()->sync([2, 3]);
        $route = sprintf('departure/release/request/%d/acknowledge', $request->id);

        $this->makeAuthenticatedApiRequest(self::METHOD_PUT, $route, $approvalData)
            ->assertStatus(422);
    }

    public function testItReturnsNotFoundIfNoReleaseToAcknowledge()
    {
        $this->makeAuthenticatedApiRequest(self::METHOD_PUT, 'departure/release/request/55/acknowledge', [])
            ->assertNotFound();
    }

    public function testReleasesCannotBeAcknowledgedByUnauthenticatedUsers()
    {
        $this->makeUnauthenticatedApiRequest(self::METHOD_PUT, 'departure/release/request/1/acknowledge', [])
            ->assertUnauthorized();
    }
}
