<?php

namespace App\Services;

use App\BaseFunctionalTestCase;
use App\Events\DepartureReleaseAcknowledgedEvent;
use App\Events\DepartureReleaseApprovedEvent;
use App\Events\DepartureReleaseRejectedEvent;
use App\Events\DepartureReleaseRequestCancelledEvent;
use App\Events\DepartureReleaseRequestedEvent;
use App\Exceptions\Release\Departure\DepartureReleaseDecisionNotAllowedException;
use App\Models\Release\Departure\DepartureReleaseRequest;
use Carbon\Carbon;
use Carbon\CarbonImmutable;

class DepartureReleaseServiceTest extends BaseFunctionalTestCase
{
    private DepartureReleaseService $service;

    public function setUp(): void
    {
        parent::setUp();
        $this->service = $this->app->make(DepartureReleaseService::class);
        Carbon::setTestNow(Carbon::now()->startOfSecond());
    }

    public function testItCreatesADepartureReleaseRequest()
    {
        $this->expectsEvents(DepartureReleaseRequestedEvent::class);
        $this->service->makeReleaseRequest(
            'BAW123',
            self::ACTIVE_USER_CID,
            1,
            2,
            125
        );

        $this->assertDatabaseHas(
            'departure_release_requests',
            [
                'callsign' => 'BAW123',
                'user_id' => self::ACTIVE_USER_CID,
                'controller_position_id' => 1,
                'target_controller_position_id' => 2,
                'expires_at' => Carbon::now()->addSeconds(125),
                'released_by' => null,
                'released_at' => null,
                'release_expires_at' => null,
                'rejected_at' => null,
                'acknowledged_by' => null,
                'acknowledged_at' => null,
                'release_valid_from' => null,
            ]
        );
    }

    public function testItThrowsExceptionIfControllerCannotApproveRequest()
    {
        $this->expectException(DepartureReleaseDecisionNotAllowedException::class);
        $request = DepartureReleaseRequest::create(
            [
                'callsign' => 'BAW123',
                'user_id' => self::ACTIVE_USER_CID,
                'controller_position_id' => 1,
                'target_controller_position_id' => 2,
                'expires_at' => Carbon::now()->addMinutes(2),
            ]
        );

        $this->service->approveReleaseRequest($request, 55, self::ACTIVE_USER_CID, 125, CarbonImmutable::now());
    }

    public function testItApprovesADepartureRelease()
    {
        $this->expectsEvents(DepartureReleaseApprovedEvent::class);
        $request = DepartureReleaseRequest::create(
            [
                'callsign' => 'BAW123',
                'user_id' => self::ACTIVE_USER_CID,
                'controller_position_id' => 1,
                'target_controller_position_id' => 2,
                'expires_at' => Carbon::now()->addMinutes(2),
            ]
        );

        $this->service->approveReleaseRequest($request, 2, self::ACTIVE_USER_CID, 125, CarbonImmutable::now()->addMinutes(3));

        $this->assertDatabaseHas(
            'departure_release_requests',
            [
                'id' => $request->id,
                'controller_position_id' => 1,
                'target_controller_position_id' => 2,
                'released_by' => self::ACTIVE_USER_CID,
                'released_at' => Carbon::now()->toDateTimeString(),
                'release_expires_at' => Carbon::now()->addMinutes(3)->addSeconds(125)->toDateTimeString(),
                'rejected_at' => null,
                'acknowledged_at' => null,
                'acknowledged_by' => null,
                'release_valid_from' => Carbon::now()->addMinutes(3)->toDateTimeString(),
            ]
        );
    }

    public function testItThrowsExceptionIfControllerCannotRejectRequest()
    {
        $this->expectException(DepartureReleaseDecisionNotAllowedException::class);
        $request = DepartureReleaseRequest::create(
            [
                'callsign' => 'BAW123',
                'user_id' => self::ACTIVE_USER_CID,
                'controller_position_id' => 1,
                'target_controller_position_id' => 2,
                'expires_at' => Carbon::now()->addMinutes(2),
            ]
        );

        $this->service->rejectReleaseRequest($request, 55, self::ACTIVE_USER_CID);
    }

    public function testItRejectsADepartureRelease()
    {
        $this->expectsEvents(DepartureReleaseRejectedEvent::class);
        $request = DepartureReleaseRequest::create(
            [
                'callsign' => 'BAW123',
                'user_id' => self::ACTIVE_USER_CID,
                'controller_position_id' => 1,
                'target_controller_position_id' => 2,
                'expires_at' => Carbon::now()->addMinutes(2),
            ]
        );

        $this->service->rejectReleaseRequest($request, 2, self::ACTIVE_USER_CID);

        $this->assertDatabaseHas(
            'departure_release_requests',
            [
                'id' => $request->id,
                'controller_position_id' => 1,
                'target_controller_position_id' => 2,
                'rejected_by' => self::ACTIVE_USER_CID,
                'rejected_at' => Carbon::now()->toDateTimeString(),
                'released_by' => null,
                'released_at' => null,
                'release_expires_at' => null,
                'acknowledged_at' => null,
                'acknowledged_by' => null,
                'release_valid_from' => null,
            ]
        );
    }

    public function testItThrowsExceptionIfControllerCannotAcknowledgeRequest()
    {
        $this->expectException(DepartureReleaseDecisionNotAllowedException::class);
        $request = DepartureReleaseRequest::create(
            [
                'callsign' => 'BAW123',
                'user_id' => self::ACTIVE_USER_CID,
                'controller_position_id' => 1,
                'target_controller_position_id' => 2,
                'expires_at' => Carbon::now()->addMinutes(2),
            ]
        );

        $this->service->acknowledgeReleaseRequest($request, 55, self::ACTIVE_USER_CID);
    }

    public function testItAcknowledgesADepartureRelease()
    {
        $this->expectsEvents(DepartureReleaseAcknowledgedEvent::class);
        $request = DepartureReleaseRequest::create(
            [
                'callsign' => 'BAW123',
                'user_id' => self::ACTIVE_USER_CID,
                'controller_position_id' => 1,
                'target_controller_position_id' => 2,
                'expires_at' => Carbon::now()->addMinutes(2),
            ]
        );

        $this->service->acknowledgeReleaseRequest($request, 2, self::ACTIVE_USER_CID);

        $this->assertDatabaseHas(
            'departure_release_requests',
            [
                'id' => $request->id,
                'controller_position_id' => 1,
                'target_controller_position_id' => 2,
                'rejected_by' => null,
                'rejected_at' => null,
                'released_by' => null,
                'released_at' => null,
                'release_expires_at' => null,
                'acknowledged_at' => Carbon::now()->toDateTimeString(),
                'acknowledged_by' => self::ACTIVE_USER_CID,
                'release_valid_from' => null,
            ]
        );
    }

    public function testItCancelsADepartureReleaseRequest()
    {
        $this->expectsEvents(DepartureReleaseRequestCancelledEvent::class);
        $request = DepartureReleaseRequest::create(
            [
                'callsign' => 'BAW123',
                'user_id' => self::ACTIVE_USER_CID,
                'controller_position_id' => 1,
                'target_controller_position_id' => 2,
                'expires_at' => Carbon::now()->addMinutes(2),
            ]
        );

        $this->service->cancelReleaseRequest($request, self::ACTIVE_USER_CID);
        $this->assertSoftDeleted($request);
    }

    public function testOnlyTheRequestingUserCanCancelARequest()
    {
        $this->expectException(DepartureReleaseDecisionNotAllowedException::class);
        $this->doesntExpectEvents(DepartureReleaseRequestCancelledEvent::class);
        $request = DepartureReleaseRequest::create(
            [
                'callsign' => 'BAW123',
                'user_id' => self::ACTIVE_USER_CID,
                'controller_position_id' => 1,
                'target_controller_position_id' => 2,
                'expires_at' => Carbon::now()->addMinutes(2),
            ]
        );

        $this->service->cancelReleaseRequest($request, self::BANNED_USER_CID);
    }
}
