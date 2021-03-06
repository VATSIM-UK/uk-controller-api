<?php

namespace App\Services;

use App\BaseFunctionalTestCase;
use App\Events\Prenote\NewPrenoteMessageEvent;
use App\Events\Prenote\PrenoteAcknowledgedEvent;
use App\Events\Prenote\PrenoteDeletedEvent;
use App\Exceptions\Prenote\PrenoteAcknowledgementNotAllowedException;
use App\Exceptions\Prenote\PrenoteAlreadyAcknowledgedException;
use App\Exceptions\Prenote\PrenoteCancellationNotAllowedException;
use App\Helpers\Prenote\CreatePrenoteMessageData;
use App\Models\Controller\Prenote;
use App\Models\Prenote\PrenoteMessage;
use App\Models\Vatsim\NetworkAircraft;
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
                CreatePrenoteMessageData::fromRequest(
                    [
                        'callsign' => 'BAW123',
                        'departure_airfield' => 'EGLL',
                        'departure_sid' => 'MODMI1G',
                        'destination_airfield' => 'EGJJ',
                        'requesting_controller_id' => 1,
                        'target_controller_id' => 2,
                        'expires_in_seconds' => 15,
                    ],
                    self::ACTIVE_USER_CID
                )
            )
        );
    }

    public function testItCreatesAPrenoteMessage()
    {
        $prenoteId = $this->service->createPrenoteMessage(
            CreatePrenoteMessageData::fromRequest(
                [
                    'callsign' => 'BAW123',
                    'departure_airfield' => 'EGLL',
                    'departure_sid' => 'MODMI1G',
                    'destination_airfield' => 'EGJJ',
                    'requesting_controller_id' => 1,
                    'target_controller_id' => 2,
                    'expires_in_seconds' => 15,
                ],
                self::ACTIVE_USER_CID
            )
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
            CreatePrenoteMessageData::fromRequest(
                [
                    'callsign' => 'BAW123',
                    'departure_airfield' => 'EGLL',
                    'departure_sid' => null,
                    'destination_airfield' => 'EGJJ',
                    'requesting_controller_id' => 1,
                    'target_controller_id' => 2,
                    'expires_in_seconds' => 15,
                ],
                self::ACTIVE_USER_CID
            )
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
            CreatePrenoteMessageData::fromRequest(
                [
                    'callsign' => 'BAW123',
                    'departure_airfield' => 'EGLL',
                    'departure_sid' => 'MODMI1G',
                    'destination_airfield' => null,
                    'requesting_controller_id' => 1,
                    'target_controller_id' => 2,
                    'expires_in_seconds' => 15,
                ],
                self::ACTIVE_USER_CID
            )
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

    public function testItCancelsRequestsForAirborneAircraft()
    {
        Event::fake();
        $aircraft1 = NetworkAircraft::factory()->create(['groundspeed' => 49, 'altitude' => 1000]);
        $aircraft2 = NetworkAircraft::factory()->create(['groundspeed' => 50, 'altitude' => 999]);
        $aircraft3 = NetworkAircraft::factory()->create(['groundspeed' => 50, 'altitude' => 1000]);
        $aircraft4 = NetworkAircraft::factory()->create(['groundspeed' => 51, 'altitude' => 1001]);

        $prenote1 = PrenoteMessage::factory()->create(['callsign' => $aircraft1->callsign]);
        $prenote2 = PrenoteMessage::factory()->create(['callsign' => $aircraft2->callsign]);
        $prenote3 = PrenoteMessage::factory()->create(['callsign' => $aircraft3->callsign]);
        $prenote4 = PrenoteMessage::factory()->create(['callsign' => $aircraft4->callsign]);
        $prenote4a = PrenoteMessage::factory()->create(['callsign' => $aircraft4->callsign]);

        $this->service->cancelMessagesForAirborneAircraft();

        // Check models
        $this->assertNotNull(PrenoteMessage::find($prenote1->id));
        $this->assertNotNull(PrenoteMessage::find($prenote2->id));
        $this->assertSoftDeleted('prenote_messages', ['id' => $prenote3->id]);
        $this->assertSoftDeleted('prenote_messages', ['id' => $prenote4->id]);
        $this->assertSoftDeleted('prenote_messages', ['id' => $prenote4a->id]);

        // Check events
        Event::assertNotDispatched(
            PrenoteDeletedEvent::class,
            function (PrenoteDeletedEvent $event) use ($prenote1) {
                return $event->broadcastWith() === ['id' => $prenote1->id];
            }
        );

        Event::assertNotDispatched(
            PrenoteDeletedEvent::class,
            function (PrenoteDeletedEvent $event) use ($prenote2) {
                return $event->broadcastWith() === ['id' => $prenote2->id];
            }
        );

        Event::assertDispatched(
            PrenoteDeletedEvent::class,
            function (PrenoteDeletedEvent $event) use ($prenote3) {
                return $event->broadcastWith() === ['id' => $prenote3->id];
            }
        );

        Event::assertDispatched(
            PrenoteDeletedEvent::class,
            function (PrenoteDeletedEvent $event) use ($prenote4) {
                return $event->broadcastWith() === ['id' => $prenote4->id];
            }
        );

        Event::assertDispatched(
            PrenoteDeletedEvent::class,
            function (PrenoteDeletedEvent $event) use ($prenote4a) {
                return $event->broadcastWith() === ['id' => $prenote4a->id];
            }
        );
    }
}
