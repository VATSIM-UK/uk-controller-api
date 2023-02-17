<?php

namespace App\Services;

use App\BaseFunctionalTestCase;
use App\Events\DepartureReleaseAcknowledgedEvent;
use App\Events\DepartureReleaseApprovedEvent;
use App\Events\DepartureReleaseRejectedEvent;
use App\Events\DepartureReleaseRequestCancelledEvent;
use App\Events\DepartureReleaseRequestedEvent;
use App\Exceptions\Release\Departure\DepartureReleaseAlreadyDecidedException;
use App\Exceptions\Release\Departure\DepartureReleaseDecisionNotAllowedException;
use App\Models\Release\Departure\DepartureReleaseRequest;
use App\Models\Vatsim\NetworkAircraft;
use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Event;

class DepartureReleaseServiceTest extends BaseFunctionalTestCase
{
    private DepartureReleaseService $service;

    public function setUp(): void
    {
        parent::setUp();
        $this->service = $this->app->make(DepartureReleaseService::class);
        Carbon::setTestNow(Carbon::now()->startOfSecond());
        Event::fake();
    }

    public function testItCreatesADepartureReleaseRequest()
    {
        Event::assertDispatched(DepartureReleaseRequestedEvent::class);
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
                'remarks' => null,
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

        $this->service->approveReleaseRequest($request, 55, self::ACTIVE_USER_CID, 125, CarbonImmutable::now(), '');
    }

    public function testItApprovesADepartureRelease()
    {
        Event::assertDispatched(DepartureReleaseApprovedEvent::class);
        $request = DepartureReleaseRequest::create(
            [
                'callsign' => 'BAW123',
                'user_id' => self::ACTIVE_USER_CID,
                'controller_position_id' => 1,
                'target_controller_position_id' => 2,
                'expires_at' => Carbon::now()->addMinutes(2),
            ]
        );

        $this->service->approveReleaseRequest(
            $request,
            2,
            self::ACTIVE_USER_CID,
            125,
            CarbonImmutable::now()->addMinutes(3),
            'Some remarks'
        );

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
                'remarks' => 'Some remarks',
            ]
        );
    }

    public function testItApprovesADepartureReleaseWithNoExpiryTime()
    {
        Event::assertDispatched(DepartureReleaseApprovedEvent::class);
        $request = DepartureReleaseRequest::create(
            [
                'callsign' => 'BAW123',
                'user_id' => self::ACTIVE_USER_CID,
                'controller_position_id' => 1,
                'target_controller_position_id' => 2,
                'expires_at' => Carbon::now()->addMinutes(2),
            ]
        );

        $this->service->approveReleaseRequest(
            $request,
            2,
            self::ACTIVE_USER_CID,
            null,
            CarbonImmutable::now()->addMinutes(3),
            'Some remarks'
        );

        $this->assertDatabaseHas(
            'departure_release_requests',
            [
                'id' => $request->id,
                'controller_position_id' => 1,
                'target_controller_position_id' => 2,
                'released_by' => self::ACTIVE_USER_CID,
                'released_at' => Carbon::now()->toDateTimeString(),
                'release_expires_at' => null,
                'rejected_at' => null,
                'acknowledged_at' => null,
                'acknowledged_by' => null,
                'release_valid_from' => Carbon::now()->addMinutes(3)->toDateTimeString(),
                'remarks' => 'Some remarks',
            ]
        );
    }

    public function testReleasesCannotBeApprovedIfAlreadyApproved()
    {
        $this->expectException(DepartureReleaseAlreadyDecidedException::class);
        Event::assertNotDispatched(DepartureReleaseApprovedEvent::class);
        $request = DepartureReleaseRequest::create(
            [
                'callsign' => 'BAW123',
                'user_id' => self::ACTIVE_USER_CID,
                'controller_position_id' => 1,
                'target_controller_position_id' => 2,
                'expires_at' => Carbon::now()->addMinutes(2),
            ]
        );
        $request->approve(self::ACTIVE_USER_CID, 25, CarbonImmutable::now());
        $this->service->approveReleaseRequest(
            $request,
            2,
            self::ACTIVE_USER_CID,
            125,
            CarbonImmutable::now()->addMinutes(3),
            ''
        );
    }

    public function testReleasesCannotBeApprovedIfAlreadyRejected()
    {
        $this->expectException(DepartureReleaseAlreadyDecidedException::class);
        Event::assertNotDispatched(DepartureReleaseApprovedEvent::class);
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
        $this->service->approveReleaseRequest(
            $request,
            2,
            self::ACTIVE_USER_CID,
            125,
            CarbonImmutable::now()->addMinutes(3),
            ''
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

        $this->service->rejectReleaseRequest($request, 55, self::ACTIVE_USER_CID, '');
    }

    public function testItRejectsADepartureRelease()
    {
        Event::assertDispatched(DepartureReleaseRejectedEvent::class);
        $request = DepartureReleaseRequest::create(
            [
                'callsign' => 'BAW123',
                'user_id' => self::ACTIVE_USER_CID,
                'controller_position_id' => 1,
                'target_controller_position_id' => 2,
                'expires_at' => Carbon::now()->addMinutes(2),
            ]
        );

        $this->service->rejectReleaseRequest($request, 2, self::ACTIVE_USER_CID, 'Some remarks');

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
                'remarks' => 'Some remarks',
            ]
        );
    }

    public function testReleasesCannotBeRejectedIfAlreadyApproved()
    {
        $this->expectException(DepartureReleaseAlreadyDecidedException::class);
        Event::assertNotDispatched(DepartureReleaseRejectedEvent::class);
        $request = DepartureReleaseRequest::create(
            [
                'callsign' => 'BAW123',
                'user_id' => self::ACTIVE_USER_CID,
                'controller_position_id' => 1,
                'target_controller_position_id' => 2,
                'expires_at' => Carbon::now()->addMinutes(2),
            ]
        );
        $request->approve(self::ACTIVE_USER_CID, 25, CarbonImmutable::now());
        $this->service->rejectReleaseRequest($request, 2, self::ACTIVE_USER_CID, '');
    }

    public function testReleasesCannotBeRejectedIfAlreadyRejected()
    {
        $this->expectException(DepartureReleaseAlreadyDecidedException::class);
        Event::assertNotDispatched(DepartureReleaseRejectedEvent::class);
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
        $this->service->rejectReleaseRequest($request, 2, self::ACTIVE_USER_CID, '');
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
        Event::assertDispatched(DepartureReleaseAcknowledgedEvent::class);
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
                'remarks' => null,
            ]
        );
    }

    public function testReleasesCannotBeAcknowledgedIfAlreadyApproved()
    {
        $this->expectException(DepartureReleaseAlreadyDecidedException::class);
        Event::assertNotDispatched(DepartureReleaseAcknowledgedEvent::class);
        $request = DepartureReleaseRequest::create(
            [
                'callsign' => 'BAW123',
                'user_id' => self::ACTIVE_USER_CID,
                'controller_position_id' => 1,
                'target_controller_position_id' => 2,
                'expires_at' => Carbon::now()->addMinutes(2),
            ]
        );
        $request->approve(self::ACTIVE_USER_CID, 25, CarbonImmutable::now());
        $this->service->acknowledgeReleaseRequest($request, 2, self::ACTIVE_USER_CID);
    }

    public function testReleasesCannotBeAcknowledgedIfAlreadyRejected()
    {
        $this->expectException(DepartureReleaseAlreadyDecidedException::class);
        Event::assertNotDispatched(DepartureReleaseAcknowledgedEvent::class);
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
        $this->service->acknowledgeReleaseRequest($request, 2, self::ACTIVE_USER_CID);
    }

    public function testItCancelsADepartureReleaseRequest()
    {
        Event::assertDispatched(DepartureReleaseRequestCancelledEvent::class);
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
        Event::assertNotDispatched(DepartureReleaseRequestCancelledEvent::class);
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

    public function testItCancelsRequestsForAirborneAircraft()
    {
        Event::fake();
        $aircraft1 = NetworkAircraft::factory()->create(['groundspeed' => 49, 'altitude' => 1000]);
        $aircraft2 = NetworkAircraft::factory()->create(['groundspeed' => 50, 'altitude' => 999]);
        $aircraft3 = NetworkAircraft::factory()->create(['groundspeed' => 50, 'altitude' => 1000]);
        $aircraft4 = NetworkAircraft::factory()->create(['groundspeed' => 51, 'altitude' => 1001]);

        $release1 = DepartureReleaseRequest::factory()->create(['callsign' => $aircraft1->callsign]);
        $release2 = DepartureReleaseRequest::factory()->create(['callsign' => $aircraft2->callsign]);
        $release3 = DepartureReleaseRequest::factory()->create(['callsign' => $aircraft3->callsign]);
        $release4 = DepartureReleaseRequest::factory()->create(['callsign' => $aircraft4->callsign]);
        $release4a = DepartureReleaseRequest::factory()->create(['callsign' => $aircraft4->callsign]);

        $this->service->cancelReleasesForAirborneAircraft();

        // Check models
        $this->assertNotNull(DepartureReleaseRequest::find($release1->id));
        $this->assertNotNull(DepartureReleaseRequest::find($release2->id));
        $this->assertSoftDeleted('departure_release_requests', ['id' => $release3->id]);
        $this->assertSoftDeleted('departure_release_requests', ['id' => $release4->id]);
        $this->assertSoftDeleted('departure_release_requests', ['id' => $release4a->id]);

        // Check events
        Event::assertNotDispatched(
            DepartureReleaseRequestCancelledEvent::class,
            function (DepartureReleaseRequestCancelledEvent $event) use ($release1)
            {
                return $event->broadcastWith() === ['id' => $release1->id];
            }
        );

        Event::assertNotDispatched(
            DepartureReleaseRequestCancelledEvent::class,
            function (DepartureReleaseRequestCancelledEvent $event) use ($release2)
            {
                return $event->broadcastWith() === ['id' => $release2->id];
            }
        );

        Event::assertDispatched(
            DepartureReleaseRequestCancelledEvent::class,
            function (DepartureReleaseRequestCancelledEvent $event) use ($release3)
            {
                return $event->broadcastWith() === ['id' => $release3->id];
            }
        );

        Event::assertDispatched(
            DepartureReleaseRequestCancelledEvent::class,
            function (DepartureReleaseRequestCancelledEvent $event) use ($release4)
            {
                return $event->broadcastWith() === ['id' => $release4->id];
            }
        );

        Event::assertDispatched(
            DepartureReleaseRequestCancelledEvent::class,
            function (DepartureReleaseRequestCancelledEvent $event) use ($release4a)
            {
                return $event->broadcastWith() === ['id' => $release4a->id];
            }
        );
    }
}
