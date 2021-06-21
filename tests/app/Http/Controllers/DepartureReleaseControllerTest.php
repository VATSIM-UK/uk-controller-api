<?php

namespace App\Http\Controllers;

use App\BaseApiTestCase;
use App\Models\Release\Departure\DepartureReleaseRequest;
use Carbon\Carbon;
use Carbon\CarbonImmutable;

class DepartureReleaseControllerTest extends BaseApiTestCase
{
    public function setUp(): void
    {
        parent::setUp();
        Carbon::setTestNow(Carbon::now()->startOfSecond());
        CarbonImmutable::setTestNow(Carbon::now()->startOfSecond());
        $this->withoutEvents();
    }

    public function testItRequestsARelease()
    {
        $requestData = [
            'callsign' => 'BAW123',
            'requesting_controller_id' => 1,
            'target_controller_id' => 2,
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
                'target_controller_position_id' => 2,
                'expires_at' => Carbon::now()->addSeconds(125)
            ]
        );
    }

    public function badReleaseRequestProvider(): array
    {
        return [
            'Missing callsign' => [
                [
                    'requesting_controller_id' => 1,
                    'target_controller_id' => 2,
                    'expires_in_seconds' => 125,
                ]
            ],
            'Callsign not string' => [
                [
                    'callsign' => 123,
                    'requesting_controller_id' => 1,
                    'target_controller_id' => 2,
                    'expires_in_seconds' => 125,
                ]
            ],
            'Missing requesting controller_id' => [
                [
                    'callsign' => 'BAW123',
                    'target_controller_id' => 2,
                    'expires_in_seconds' => 125,
                ]
            ],
            'Requesting controller id not integer' => [
                [
                    'callsign' => 'BAW123',
                    'requesting_controller_id' => 'abc',
                    'target_controller_id' => 2,
                    'expires_in_seconds' => 125,
                ]
            ],
            'Requesting controller not a valid controller' => [
                [
                    'callsign' => 'BAW123',
                    'requesting_controller_id' => 12345,
                    'target_controller_id' => 2,
                    'expires_in_seconds' => 125,
                ]
            ],
            'Requesting controller cannot request releases' => [
                [
                    'callsign' => 'BAW123',
                    'requesting_controller_id' => 4,
                    'target_controller_id' => 2,
                    'expires_in_seconds' => 125,
                ]
            ],
            'Requesting controller is requesting release of themselves' => [
                [
                    'callsign' => 'BAW123',
                    'requesting_controller_id' => 2,
                    'target_controller_id' => 2,
                    'expires_in_seconds' => 125,
                ]
            ],
            'Target controller missing' => [
                [
                    'callsign' => 'BAW123',
                    'requesting_controller_id' => 1,
                    'expires_in_seconds' => 125,
                ]
            ],
            'Target controller not an integer' => [
                [
                    'callsign' => 'BAW123',
                    'requesting_controller_id' => 1,
                    'target_controller_id' => 'abc',
                    'expires_in_seconds' => 125,
                ]
            ],
            'Target controller not a valid controller' => [
                [
                    'callsign' => 'BAW123',
                    'requesting_controller_id' => 1,
                    'target_controller_id' => 12345,
                    'expires_in_seconds' => 125,
                ]
            ],
            'Target controller cannot receive releases' => [
                [
                    'callsign' => 'BAW123',
                    'requesting_controller_id' => 1,
                    'target_controller_id' => 4,
                    'expires_in_seconds' => 125,
                ]
            ],
            'Missing expires in seconds' => [
                [
                    'callsign' => 'BAW123',
                    'requesting_controller_id' => 1,
                    'target_controller_id' => 2,
                ]
            ],
            'Expires in seconds not integer' => [
                [
                    'callsign' => 'BAW123',
                    'requesting_controller_id' => 1,
                    'target_controller_id' => 2,
                    'expires_in_seconds' => 'abc',
                ]
            ],
            'Expires in seconds too low' => [
                [
                    'callsign' => 'BAW123',
                    'requesting_controller_id' => 1,
                    'target_controller_id' => 2,
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

    public function testItDoesntCreateAReleaseIfOneAlreadyActive()
    {
        DepartureReleaseRequest::create(
            [
                'callsign' => 'BAW123',
                'user_id' => self::ACTIVE_USER_CID,
                'controller_position_id' => 1,
                'target_controller_position_id' => 2,
                'expires_at' => Carbon::now()->addMinutes(2),
            ]
        );

        $requestData = [
            'callsign' => 'BAW123',
            'requesting_controller_id' => 1,
            'target_controller_id' => 2,
            'expires_in_seconds' => 125,
        ];

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
                'target_controller_position_id' => 2,
                'expires_at' => Carbon::now()->addMinutes(2),
            ]
        );
        $route = sprintf('departure/release/request/%d/approve', $request->id);

        $approvalData = [
            'controller_position_id' => 2,
            'expires_in_seconds' => 10,
            'released_at' => null,
        ];

        $this->makeAuthenticatedApiRequest(self::METHOD_PATCH, $route, $approvalData)
            ->assertOk();

        $this->assertDatabaseHas(
            'departure_release_requests',
            [
                'id' => $request->id,
                'released_by' => self::ACTIVE_USER_CID,
                'release_expires_at' => Carbon::now()->addSeconds(10)->toDateTimeString(),
                'release_valid_from' => Carbon::now()->toDateTimeString(),
            ]
        );
    }

    public function testItApprovesAReleaseWithNoExpiryTime()
    {
        $request = DepartureReleaseRequest::create(
            [
                'callsign' => 'BAW123',
                'user_id' => self::ACTIVE_USER_CID,
                'controller_position_id' => 1,
                'target_controller_position_id' => 2,
                'expires_at' => Carbon::now()->addMinutes(2),
            ]
        );
        $route = sprintf('departure/release/request/%d/approve', $request->id);

        $approvalData = [
            'controller_position_id' => 2,
            'expires_in_seconds' => null,
            'released_at' => null,
        ];

        $this->makeAuthenticatedApiRequest(self::METHOD_PATCH, $route, $approvalData)
            ->assertOk();

        $this->assertDatabaseHas(
            'departure_release_requests',
            [
                'id' => $request->id,
                'released_by' => self::ACTIVE_USER_CID,
                'release_expires_at' => null,
                'release_valid_from' => Carbon::now()->toDateTimeString(),
            ]
        );
    }

    public function testItApprovesAReleaseWithValidFromTime()
    {
        $request = DepartureReleaseRequest::create(
            [
                'callsign' => 'BAW123',
                'user_id' => self::ACTIVE_USER_CID,
                'controller_position_id' => 1,
                'target_controller_position_id' => 2,
                'expires_at' => Carbon::now()->addMinutes(2),
            ]
        );

        $route = sprintf('departure/release/request/%d/approve', $request->id);

        $approvalData = [
            'controller_position_id' => 2,
            'expires_in_seconds' => 10,
            'released_at' => Carbon::now()->addMinutes(2)->toDateTimeString(),
        ];

        $this->makeAuthenticatedApiRequest(self::METHOD_PATCH, $route, $approvalData)
            ->assertOk();

        $this->assertDatabaseHas(
            'departure_release_requests',
            [
                'id' => $request->id,
                'released_by' => self::ACTIVE_USER_CID,
                'release_expires_at' => Carbon::now()->addMinutes(2)->addSeconds(10)->toDateTimeString(),
                'release_valid_from' => Carbon::now()->addMinutes(2)->toDateTimeString(),
            ]
        );
    }

    public function badApprovalDataProvider(): array
    {
        return [
            'Controller position id missing' => [
                [
                    'expires_in_seconds' => 10,
                    'released_at' => null,
                ]
            ],
            'Controller position id not integer' => [
                [
                    'controller_position_id' => 'abc',
                    'expires_in_seconds' => 10,
                    'released_at' => null,
                ]
            ],
            'Expires in seconds missing' => [
                [
                    'controller_position_id' => 2,
                    'released_at' => null,
                ]
            ],
            'Expires in seconds not an integer' => [
                [
                    'controller_position_id' => 2,
                    'expires_in_seconds' => 'abc',
                    'released_at' => null,
                ]
            ],
            'Expires in seconds too low' => [
                [
                    'controller_position_id' => 2,
                    'expires_in_seconds' => 0,
                    'released_at' => null,
                ]
            ],
            'Controller position invalid' => [
                [
                    'controller_position_id' => 55,
                    'expires_in_seconds' => 10,
                    'released_at' => null,
                ]
            ],
            'Released at missing' => [
                [
                    'controller_position_id' => 2,
                    'expires_in_seconds' => 10,
                ]
            ],
            'Released at not a date' => [
                [
                    'controller_position_id' => 2,
                    'expires_in_seconds' => 10,
                    'released_at' => 'abc',
                ]
            ],
            'Released at invalid date' => [
                [
                    'controller_position_id' => 2,
                    'expires_in_seconds' => 10,
                    'released_at' => Carbon::now()->addMinutes(5)->toDateTimeString('minute'),
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
                'target_controller_position_id' => 2,
                'expires_at' => Carbon::now()->addMinutes(2),
            ]
        );
        $route = sprintf('departure/release/request/%d/approve', $request->id);

        $this->makeAuthenticatedApiRequest(self::METHOD_PATCH, $route, $approvalData)
            ->assertStatus(422);
    }

    public function testItReturnsNotFoundIfNoReleaseToApprove()
    {
        $this->makeAuthenticatedApiRequest(self::METHOD_PATCH, 'departure/release/request/55/approve', [])
            ->assertNotFound();
    }

    public function testReleasesCannotBeApprovedByWrongControllerPosition()
    {
        $request = DepartureReleaseRequest::create(
            [
                'callsign' => 'BAW123',
                'user_id' => self::ACTIVE_USER_CID,
                'controller_position_id' => 1,
                'target_controller_position_id' => 2,
                'expires_at' => Carbon::now()->addMinutes(2),
            ]
        );
        $route = sprintf('departure/release/request/%d/approve', $request->id);

        $approvalData = [
            'controller_position_id' => 3,
            'expires_in_seconds' => 10,
            'released_at' => Carbon::now()->addMinutes(2)->toDateTimeString(),
        ];

        $this->makeAuthenticatedApiRequest(
            self::METHOD_PATCH,
            $route,
            $approvalData
        )->assertForbidden();
    }

    public function testItReturnsConflictOnApprovalIfDecisionAlreadyMade()
    {
        $request = DepartureReleaseRequest::create(
            [
                'callsign' => 'BAW123',
                'user_id' => self::ACTIVE_USER_CID,
                'controller_position_id' => 1,
                'target_controller_position_id' => 2,
                'expires_at' => Carbon::now()->addMinutes(2),
            ]
        );
        $request->reject(self::ACTIVE_USER_CID);
        $route = sprintf('departure/release/request/%d/approve', $request->id);

        $approvalData = [
            'controller_position_id' => 2,
            'expires_in_seconds' => 10,
            'released_at' => Carbon::now()->addMinutes(2)->toDateTimeString(),
        ];

        $this->makeAuthenticatedApiRequest(
            self::METHOD_PATCH,
            $route,
            $approvalData
        )->assertStatus(409);
    }

    public function testReleasesCannotBeApprovedByUnauthenticatedUsers()
    {
        $this->makeUnauthenticatedApiRequest(self::METHOD_PATCH, 'departure/release/request/1/approve', [])
            ->assertUnauthorized();
    }

    public function testItRejectsARelease()
    {
        $request = DepartureReleaseRequest::create(
            [
                'callsign' => 'BAW123',
                'user_id' => self::ACTIVE_USER_CID,
                'controller_position_id' => 1,
                'target_controller_position_id' => 2,
                'expires_at' => Carbon::now()->addMinutes(2),
            ]
        );
        $route = sprintf('departure/release/request/%d/reject', $request->id);

        $rejectionData = [
            'controller_position_id' => 2,
        ];

        $this->makeAuthenticatedApiRequest(self::METHOD_PATCH, $route, $rejectionData)
            ->assertOk();

        $this->assertDatabaseHas(
            'departure_release_requests',
            [
                'id' => $request->id,
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
    public function testReleasesCannotBeRejectedOnBadData(array $rejectedData)
    {
        $request = DepartureReleaseRequest::create(
            [
                'callsign' => 'BAW123',
                'user_id' => self::ACTIVE_USER_CID,
                'controller_position_id' => 1,
                'target_controller_position_id' => 2,
                'expires_at' => Carbon::now()->addMinutes(2),
            ]
        );
        $route = sprintf('departure/release/request/%d/reject', $request->id);

        $this->makeAuthenticatedApiRequest(self::METHOD_PATCH, $route, $rejectedData)
            ->assertStatus(422);
    }

    public function testItReturnsNotFoundIfNoReleaseToReject()
    {
        $this->makeAuthenticatedApiRequest(self::METHOD_PATCH, 'departure/release/request/55/reject', [])
            ->assertNotFound();
    }

    public function testReleasesCannotBeRejectedByWrongControllerPosition()
    {
        $request = DepartureReleaseRequest::create(
            [
                'callsign' => 'BAW123',
                'user_id' => self::ACTIVE_USER_CID,
                'controller_position_id' => 1,
                'target_controller_position_id' => 2,
                'expires_at' => Carbon::now()->addMinutes(2),
            ]
        );
        $route = sprintf('departure/release/request/%d/reject', $request->id);

        $rejectedData = [
            'controller_position_id' => 3,
        ];

        $this->makeAuthenticatedApiRequest(
            self::METHOD_PATCH,
            $route,
            $rejectedData
        )->assertForbidden();
    }

    public function testItReturnsConflictOnRejectionIfDecisionAlreadyMade()
    {
        $request = DepartureReleaseRequest::create(
            [
                'callsign' => 'BAW123',
                'user_id' => self::ACTIVE_USER_CID,
                'controller_position_id' => 1,
                'target_controller_position_id' => 2,
                'expires_at' => Carbon::now()->addMinutes(2),
            ]
        );
        $request->reject(self::ACTIVE_USER_CID);
        $route = sprintf('departure/release/request/%d/reject', $request->id);

        $rejectionData = [
            'controller_position_id' => 2,
        ];

        $this->makeAuthenticatedApiRequest(
            self::METHOD_PATCH,
            $route,
            $rejectionData
        )->assertStatus(409);
    }

    public function testReleasesCannotBeRejectedByUnauthenticatedUsers()
    {
        $this->makeUnauthenticatedApiRequest(self::METHOD_PATCH, 'departure/release/request/1/reject', [])
            ->assertUnauthorized();
    }

    public function testItAcknowledgesARelease()
    {
        $request = DepartureReleaseRequest::create(
            [
                'callsign' => 'BAW123',
                'user_id' => self::ACTIVE_USER_CID,
                'controller_position_id' => 1,
                'target_controller_position_id' => 2,
                'expires_at' => Carbon::now()->addMinutes(2),
            ]
        );
        $route = sprintf('departure/release/request/%d/acknowledge', $request->id);

        $rejectionData = [
            'controller_position_id' => 2,
        ];

        $this->makeAuthenticatedApiRequest(self::METHOD_PATCH, $route, $rejectionData)
            ->assertOk();

        $this->assertDatabaseHas(
            'departure_release_requests',
            [
                'id' => $request->id,
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
    public function testReleasesCannotBeAcknowledgedOnBadData(array $acknowledgeData)
    {
        $request = DepartureReleaseRequest::create(
            [
                'callsign' => 'BAW123',
                'user_id' => self::ACTIVE_USER_CID,
                'controller_position_id' => 1,
                'target_controller_position_id' => 2,
                'expires_at' => Carbon::now()->addMinutes(2),
            ]
        );
        $route = sprintf('departure/release/request/%d/acknowledge', $request->id);

        $this->makeAuthenticatedApiRequest(self::METHOD_PATCH, $route, $acknowledgeData)
            ->assertStatus(422);
    }

    public function testItReturnsNotFoundIfNoReleaseToAcknowledge()
    {
        $this->makeAuthenticatedApiRequest(self::METHOD_PATCH, 'departure/release/request/55/acknowledge', [])
            ->assertNotFound();
    }

    public function testReleasesCannotBeAcknowledgedByWrongControllerPosition()
    {
        $request = DepartureReleaseRequest::create(
            [
                'callsign' => 'BAW123',
                'user_id' => self::ACTIVE_USER_CID,
                'controller_position_id' => 1,
                'target_controller_position_id' => 2,
                'expires_at' => Carbon::now()->addMinutes(2),
            ]
        );
        $route = sprintf('departure/release/request/%d/acknowledge', $request->id);

        $acknowledgeData = [
            'controller_position_id' => 3,
        ];

        $this->makeAuthenticatedApiRequest(
            self::METHOD_PATCH,
            $route,
            $acknowledgeData
        )->assertForbidden();
    }

    public function testItReturnsConflictOnAcknowledgementIfDecisionAlreadyMade()
    {
        $request = DepartureReleaseRequest::create(
            [
                'callsign' => 'BAW123',
                'user_id' => self::ACTIVE_USER_CID,
                'controller_position_id' => 1,
                'target_controller_position_id' => 2,
                'expires_at' => Carbon::now()->addMinutes(2),
            ]
        );
        $request->reject(self::ACTIVE_USER_CID);
        $route = sprintf('departure/release/request/%d/acknowledge', $request->id);

        $rejectionData = [
            'controller_position_id' => 2,
        ];

        $this->makeAuthenticatedApiRequest(
            self::METHOD_PATCH,
            $route,
            $rejectionData
        )->assertStatus(409);
    }

    public function testReleasesCannotBeAcknowledgedByUnauthenticatedUsers()
    {
        $this->makeUnauthenticatedApiRequest(self::METHOD_PATCH, 'departure/release/request/1/acknowledge', [])
            ->assertUnauthorized();
    }

    public function testItCancelsARelease()
    {
        $request = DepartureReleaseRequest::create(
            [
                'callsign' => 'BAW123',
                'user_id' => self::ACTIVE_USER_CID,
                'controller_position_id' => 1,
                'target_controller_position_id' => 2,
                'expires_at' => Carbon::now()->addMinutes(2),
            ]
        );
        $route = sprintf('departure/release/request/%d', $request->id);

        $this->makeAuthenticatedApiRequest(self::METHOD_DELETE, $route)
            ->assertOk();

        $this->assertSoftDeleted('departure_release_requests', ['id' => $request->id]);
    }

    public function testItReturnsForbiddenIfReleaseCancelledByNonRequestingUser()
    {
        $request = DepartureReleaseRequest::create(
            [
                'callsign' => 'BAW123',
                'user_id' => self::BANNED_USER_CID,
                'controller_position_id' => 1,
                'target_controller_position_id' => 2,
                'expires_at' => Carbon::now()->addMinutes(2),
            ]
        );
        $route = sprintf('departure/release/request/%d', $request->id);

        $this->makeAuthenticatedApiRequest(self::METHOD_DELETE, $route)
            ->assertForbidden();
    }

    public function testItReturnsNotFoundIfNoReleaseToCancel()
    {
        $this->makeAuthenticatedApiRequest(self::METHOD_DELETE, 'departure/release/request/55')
            ->assertNotFound();
    }
}
