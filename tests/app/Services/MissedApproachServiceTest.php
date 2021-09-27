<?php

namespace App\Services;

use App\BaseFunctionalTestCase;
use App\Events\MissedApproach\MissedApproachEvent;
use App\Exceptions\MissedApproach\MissedApproachAlreadyActiveException;
use App\Models\MissedApproach\MissedApproachNotification;
use Carbon\Carbon;
use Illuminate\Support\Facades\Event;
use TestingUtils\Traits\WithSeedUsers;

class MissedApproachServiceTest extends BaseFunctionalTestCase
{
    use WithSeedUsers;

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
}
