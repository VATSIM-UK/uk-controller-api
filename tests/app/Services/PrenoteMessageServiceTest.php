<?php

namespace App\Services;

use App\BaseFunctionalTestCase;
use App\Events\Prenote\NewPrenoteMessageEvent;
use App\Events\Prenote\PrenoteAcknowledgedEvent;
use App\Events\Prenote\PrenoteDeletedEvent;
use App\Exceptions\Prenote\PrenoteAcknowledgementNotAllowedException;
use App\Exceptions\Prenote\PrenoteAlreadyAcknowledgedException;
use App\Exceptions\Prenote\PrenoteCancellationNotAllowedException;
use App\Models\Controller\Prenote;
use App\Models\Prenote\PrenoteMessage;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;
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

    public function createPrenoteMessage(): PrenoteMessage
    {
        return PrenoteMessage::findOrfail(
            $this->service->createPrenoteMessage(
                'BAW123',
                'EGLL',
                'MODMI1G',
                'EGJJ',
                self::ACTIVE_USER_CID,
                1,
                2,
                15
            )
        );
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

        Event::assertDispatched(
            function (NewPrenoteMessageEvent $event) use ($prenoteId) {
                return $event->broadcastWith() === [
                        'id' => $prenoteId,
                        'callsign' => 'BAW123',
                        'departure_airfield' => 'EGLL',
                        'departure_sid' => 'MODMI1G',
                        'destination_airfield' => 'EGJJ',
                        'sending_controller' => 1,
                        'target_controller' => 2,
                        'expires_at' => Carbon::now()->addSeconds(15)->toDateTimeString(),
                    ];
            }
        );
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

        Event::assertDispatched(
            function (NewPrenoteMessageEvent $event) use ($prenoteId) {
                return $event->broadcastWith() === [
                        'id' => $prenoteId,
                        'callsign' => 'BAW123',
                        'departure_airfield' => 'EGLL',
                        'departure_sid' => null,
                        'destination_airfield' => 'EGJJ',
                        'sending_controller' => 1,
                        'target_controller' => 2,
                        'expires_at' => Carbon::now()->addSeconds(15)->toDateTimeString(),
                    ];
            }
        );
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

        Event::assertDispatched(
            function (NewPrenoteMessageEvent $event) use ($prenoteId) {
                return $event->broadcastWith() === [
                        'id' => $prenoteId,
                        'callsign' => 'BAW123',
                        'departure_airfield' => 'EGLL',
                        'departure_sid' => 'MODMI1G',
                        'destination_airfield' => null,
                        'sending_controller' => 1,
                        'target_controller' => 2,
                        'expires_at' => Carbon::now()->addSeconds(15)->toDateTimeString(),
                    ];
            }
        );
    }

    public function testItAcknowledgesAPrenote()
    {
        $prenote = $this->createPrenoteMessage();
        $this->service->acknowledgePrenoteMessage(
            $prenote,
            self::ACTIVE_USER_CID,
            2
        );

        $this->assertDatabaseHas(
            'prenote_messages',
            [
                'id' => $prenote->id,
                'callsign' => 'BAW123',
                'acknowledged_at' => Carbon::now()->toDateTimeString(),
                'acknowledged_by' => self::ACTIVE_USER_CID,
            ]
        );

        Event::assertDispatched(
            function (PrenoteAcknowledgedEvent $event) use ($prenote) {
                return $event->broadcastWith() === [
                        'id' => $prenote->id,
                    ];
            }
        );
    }

    public function testItThrowsExceptionIfControllerIsntTargetOfPrenote()
    {
        $this->expectException(PrenoteAcknowledgementNotAllowedException::class);
        $this->service->acknowledgePrenoteMessage($this->createPrenoteMessage(), self::ACTIVE_USER_CID, 1);
    }

    public function testItThrowsExceptionIfPrenoteAlreadyAcknowledged()
    {
        $this->expectException(PrenoteAlreadyAcknowledgedException::class);
        $this->service->acknowledgePrenoteMessage(
            $this->createPrenoteMessage()->acknowledge(self::ACTIVE_USER_CID),
            self::ACTIVE_USER_CID,
            2
        );
    }

    public function testItDeletesAPrenote()
    {
        $prenote = $this->createPrenoteMessage();
        $this->service->cancelPrenoteMessage(
            $prenote,
            self::ACTIVE_USER_CID
        );

        $this->assertSoftDeleted($prenote);

        Event::assertDispatched(
            function (PrenoteDeletedEvent $event) use ($prenote) {
                return $event->broadcastWith() === [
                        'id' => $prenote->id,
                    ];
            }
        );
    }

    public function testOnlyTheUserCreatingAPrenoteCanDeleteIt()
    {
        $this->expectException(PrenoteCancellationNotAllowedException::class);
        $prenote = $this->createPrenoteMessage();
        $this->service->cancelPrenoteMessage(
            $prenote,
            1234
        );
    }
}
