<?php

namespace App\Services;

use App\BaseFunctionalTestCase;
use App\Events\DepartureReleaseRequestedEvent;
use App\Models\Release\Departure\DepartureReleaseRequest;
use Carbon\Carbon;

class DepartureReleaseServiceTest extends BaseFunctionalTestCase
{
    private DepartureReleaseService $service;

    public function setUp(): void
    {
        parent::setUp();
        $this->service = $this->app->make(DepartureReleaseService::class);
        Carbon::setTestNow(Carbon::now());
    }

    public function testItCreatesADepartureReleaseRequest()
    {
        $this->expectsEvents(DepartureReleaseRequestedEvent::class);
        $this->service->makeReleaseRequest(
            'BAW123',
            self::ACTIVE_USER_CID,
            1,
            [2, 3],
            125
        );
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
                'released_by' => null,
                'released_at' => null,
                'release_expires_at' => null,
                'rejected_at' => null,
            ]
        );

        $this->assertDatabaseHas(
            'controller_position_departure_release_request',
            [
                'departure_release_request_id' => $latestRelease,
                'controller_position_id' => 3,
                'released_by' => null,
                'released_at' => null,
                'release_expires_at' => null,
                'rejected_at' => null,
            ]
        );
    }
}
