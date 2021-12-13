<?php

namespace App\Services;

use App\BaseFunctionalTestCase;
use App\Events\MissedApproach\MissedApproachAcknowledgedEvent;
use App\Events\MissedApproach\MissedApproachEvent;
use App\Exceptions\MissedApproach\CannotAcknowledgeMissedApproachException;
use App\Exceptions\MissedApproach\MissedApproachAlreadyActiveException;
use App\Models\MissedApproach\MissedApproachNotification;
use Carbon\Carbon;
use Illuminate\Support\Facades\Event;
use TestingUtils\Traits\WithSeedUsers;
use util\Traits\WithNetworkController;

class MissedApproachServiceTest extends BaseFunctionalTestCase
{
    use WithSeedUsers;
    use WithNetworkController;

    private MissedApproachService $service;

    public function setUp(): void
    {
        parent::setUp();
        Carbon::setTestNow(Carbon::now());
        Event::fake();
        $this->actingAs($this->activeUser());
        $this->service = $this->app->make(MissedApproachService::class);
    }

    public function testItThrowsAnExceptionIfAMissedApproachIsAlreadyActive()
    {
        MissedApproachNotification::create(
            ['callsign' => 'BAW123', 'user_id' => self::ACTIVE_USER_CID, 'expires_at' => Carbon::now()->addSecond()]
        );

        $this->expectException(MissedApproachAlreadyActiveException::class);
        $this->expectExceptionMessage('Missed approach already active for BAW123');
        $this->service->sendMissedApproachNotification('BAW123');
    }

    public function testItCreatesAMissedApproachNotification()
    {
        $notification = $this->service->sendMissedApproachNotification('BAW123');
        $this->assertDatabaseHas(
            'missed_approach_notifications',
            [
                'id' => $notification->id,
                'callsign' => 'BAW123',
                'expires_at' => Carbon::now()->addMinutes(3)->startOfSecond(),
            ]
        );
    }

    public function testItCreatesAMissedApproachNotificationIfAllActiveHaveExpired()
    {
        $missed = MissedApproachNotification::create(
            ['callsign' => 'BAW123', 'user_id' => self::ACTIVE_USER_CID, 'expires_at' => Carbon::now()]
        );

        $this->service->sendMissedApproachNotification('BAW123');
        $this->assertDatabaseHas(
            'missed_approach_notifications',
            [
                'id' => $missed->id + 1,
                'callsign' => 'BAW123'
            ]
        );
    }

    public function testCreatingAMissedApproachSendsAnEvent()
    {
        $this->service->sendMissedApproachNotification('BAW123');
        Event::assertDispatched(MissedApproachEvent::class, function (MissedApproachEvent $event) {
            return $event->broadcastWith()['callsign'] === 'BAW123' &&
                $event->broadcastWith()['expires_at'] === Carbon::now()->addMinutes(3)->startOfSecond(
                )->toDateTimeString();
        });
    }

    public function testItAcknowledgesMissedApproachAsEnroute()
    {
        $missed = MissedApproachNotification::create(
            ['callsign' => 'BAW123', 'user_id' => self::ACTIVE_USER_CID, 'expires_at' => Carbon::now()->addHour()]
        );

        $this->setNetworkController();
        $this->service->acknowledge($missed, 'Remarks');

        $missed->refresh();
        $this->assertEquals('Remarks', $missed->remarks);
        $this->assertEquals(self::ACTIVE_USER_CID, $missed->acknowledged_by);
        $this->assertNotNull($missed->acknowledged_at);
    }

    public function testItAcknowledgesMissedApproachAsApproach()
    {
        $missed = MissedApproachNotification::create(
            ['callsign' => 'BAW123', 'user_id' => self::ACTIVE_USER_CID, 'expires_at' => Carbon::now()->addHour()]
        );

        $this->setNetworkController(self::ACTIVE_USER_CID, 2);
        $this->service->acknowledge($missed, 'Remarks');

        $missed->refresh();
        $this->assertEquals('Remarks', $missed->remarks);
        $this->assertEquals(self::ACTIVE_USER_CID, $missed->acknowledged_by);
        $this->assertNotNull($missed->acknowledged_at);
    }

    public function testAcknowledgingTheMissedApproachGeneratesEvent()
    {
        $missed = MissedApproachNotification::create(
            ['callsign' => 'BAW123', 'user_id' => self::ACTIVE_USER_CID, 'expires_at' => Carbon::now()->addHour()]
        );

        $this->setNetworkController();
        $this->service->acknowledge($missed, 'Remarks');

        Event::assertDispatched(
            MissedApproachAcknowledgedEvent::class,
            function (MissedApproachAcknowledgedEvent $event) use ($missed) {
                return $event->broadcastWith() === [
                        'id' => $missed->id,
                        'acknowledged_by' => 'LON_S_CTR',
                        'remarks' => 'Remarks'
                    ];
            }
        );
    }

    public function testMissedApproachIsNotAcknowledgedIfUserIsNotInAirfieldTopDownOrder()
    {
        $missed = MissedApproachNotification::create(
            ['callsign' => 'BAW123', 'user_id' => self::ACTIVE_USER_CID, 'expires_at' => Carbon::now()->addHour()]
        );

        // 4 is LON_C_CTR
        $this->setNetworkController(self::ACTIVE_USER_CID, 4);

        try {
            $this->service->acknowledge($missed, 'Remarks');
        } catch (CannotAcknowledgeMissedApproachException $exception) {
            $missed->refresh();
            $this->assertNull($missed->acknowledged_at);
            Event::assertNotDispatched(MissedApproachAcknowledgedEvent::class);
            return;
        }

        $this->fail('Expected exception not thrown');
    }

    public function testMissedApproachIsNotAcknowledgedIfUserIsNotApproachOrHigher()
    {
        $missed = MissedApproachNotification::create(
            ['callsign' => 'BAW123', 'user_id' => self::ACTIVE_USER_CID, 'expires_at' => Carbon::now()->addHour()]
        );

        // 1 is EGLL_S_TWR
        $this->setNetworkController(self::ACTIVE_USER_CID, 1);

        try {
            $this->service->acknowledge($missed, 'Remarks');
        } catch (CannotAcknowledgeMissedApproachException $exception) {
            $missed->refresh();
            $this->assertNull($missed->acknowledged_at);
            Event::assertNotDispatched(MissedApproachAcknowledgedEvent::class);
            return;
        }

        $this->fail('Expected exception not thrown');
    }

    public function testMissedApproachIsNotAcknowledgedIfUserNotControlling()
    {
        $missed = MissedApproachNotification::create(
            ['callsign' => 'BAW123', 'user_id' => self::ACTIVE_USER_CID, 'expires_at' => Carbon::now()->addHour()]
        );

        try {
            $this->service->acknowledge($missed, 'Remarks');
        } catch (CannotAcknowledgeMissedApproachException $exception) {
            $missed->refresh();
            $this->assertNull($missed->acknowledged_at);
            Event::assertNotDispatched(MissedApproachAcknowledgedEvent::class);
            return;
        }

        $this->fail('Expected exception not thrown');
    }

    public function testMissedApproachIsNotAcknowledgedIfAlreadyAcknowledged()
    {
        Carbon::setTestNow(Carbon::now()->subMinutes(5)->startOfSecond());
        $missed = MissedApproachNotification::create(
            ['callsign' => 'BAW123', 'user_id' => self::ACTIVE_USER_CID, 'expires_at' => Carbon::now()->addHour()]
        );
        $missed->acknowledge(self::ACTIVE_USER_CID, 'Foo');

        $this->setNetworkController();

        try {
            $this->service->acknowledge($missed, 'Remarks');
        } catch (CannotAcknowledgeMissedApproachException $exception) {
            $missed->refresh();
            $this->assertEquals(Carbon::now(), $missed->acknowledged_at);
            Event::assertNotDispatched(MissedApproachAcknowledgedEvent::class);
            return;
        }

        $this->fail('Expected exception not thrown');
    }
}
