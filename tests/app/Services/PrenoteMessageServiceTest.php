<?php

namespace App\Services;

use App\BaseFunctionalTestCase;
use App\Events\Prenote\NewPrenoteMessageEvent;
use Carbon\Carbon;
use Illuminate\Support\Facades\Event;

class PrenoteMessageServiceTest extends BaseFunctionalTestCase
{
    private PrenoteMessageService $service;

    public function setUp(): void
    {
        parent::setUp();
        Carbon::setTestNow(Carbon::now()->startOfSecond());
        $this->service = $this->app->make(PrenoteMessageService::class);
        Event::fake();
    }

    public function testItCreatesAPrenoteMessage()
    {
        $prenoteId = $this->service->createPrenoteMessage(
            'BAW123',
            'EGLL',
            'MODMI1G',
            'EGJJ',
            self::ACTIVE_USER_CID,
            1,
            2,
            15
        );

        $this->assertDatabaseHas(
            'prenote_messages',
            [
                'id' => $prenoteId,
                'callsign' => 'BAW123',
                'departure_airfield' => 'EGLL',
                'departure_sid' => 'MODMI1G',
                'destination_airfield' => 'EGJJ',
                'user_id' => self::ACTIVE_USER_CID,
                'controller_position_id' => 1,
                'target_controller_position_id' => 2,
                'expires_at' => Carbon::now()->addSeconds(15)->toDateTimeString(),
                'acknowledged_at' => null,
                'acknowledged_by' => null,
                'deleted_at' => null,
            ]
        );

        Event::assertDispatched(function (NewPrenoteMessageEvent $event) use ($prenoteId) {
            return $event->broadcastWith() === [
                'id' => $prenoteId,
                'callsign' => 'BAW123',
                'departure_airfield' => 'EGLL',
                'departure_sid' => 'MODMI1G',
                'destination_airfield' => 'EGJJ',
                'sending_controller' => 1,
                'target_controller' => 2,
                'expires_at' => Carbon::now()->addSeconds(15)->toDateTimeString()
            ];
        });
    }

    public function testItCreatesAPrenoteMessageWithNoSid()
    {
        $prenoteId = $this->service->createPrenoteMessage(
            'BAW123',
            'EGLL',
            null,
            'EGJJ',
            self::ACTIVE_USER_CID,
            1,
            2,
            15
        );

        $this->assertDatabaseHas(
            'prenote_messages',
            [
                'id' => $prenoteId,
                'callsign' => 'BAW123',
                'departure_airfield' => 'EGLL',
                'departure_sid' => null,
                'destination_airfield' => 'EGJJ',
                'user_id' => self::ACTIVE_USER_CID,
                'controller_position_id' => 1,
                'target_controller_position_id' => 2,
                'expires_at' => Carbon::now()->addSeconds(15)->toDateTimeString(),
                'acknowledged_at' => null,
                'acknowledged_by' => null,
                'deleted_at' => null,
            ]
        );

        Event::assertDispatched(function (NewPrenoteMessageEvent $event) use ($prenoteId) {
            return $event->broadcastWith() === [
                'id' => $prenoteId,
                'callsign' => 'BAW123',
                'departure_airfield' => 'EGLL',
                'departure_sid' => null,
                'destination_airfield' => 'EGJJ',
                'sending_controller' => 1,
                'target_controller' => 2,
                'expires_at' => Carbon::now()->addSeconds(15)->toDateTimeString()
            ];
        });
    }

    public function testItCreatesAPrenoteMessageWithNoDestination()
    {
        $prenoteId = $this->service->createPrenoteMessage(
            'BAW123',
            'EGLL',
            'MODMI1G',
            null,
            self::ACTIVE_USER_CID,
            1,
            2,
            15
        );

        $this->assertDatabaseHas(
            'prenote_messages',
            [
                'id' => $prenoteId,
                'callsign' => 'BAW123',
                'departure_airfield' => 'EGLL',
                'departure_sid' => 'MODMI1G',
                'destination_airfield' => null,
                'user_id' => self::ACTIVE_USER_CID,
                'controller_position_id' => 1,
                'target_controller_position_id' => 2,
                'expires_at' => Carbon::now()->addSeconds(15)->toDateTimeString(),
                'acknowledged_at' => null,
                'acknowledged_by' => null,
                'deleted_at' => null,
            ]
        );

        Event::assertDispatched(function (NewPrenoteMessageEvent $event) use ($prenoteId) {
            return $event->broadcastWith() === [
                'id' => $prenoteId,
                'callsign' => 'BAW123',
                'departure_airfield' => 'EGLL',
                'departure_sid' => 'MODMI1G',
                'destination_airfield' => null,
                'sending_controller' => 1,
                'target_controller' => 2,
                'expires_at' => Carbon::now()->addSeconds(15)->toDateTimeString()
            ];
        });
    }
}
