<?php

namespace App\Http\Controllers;

use App\BaseApiTestCase;
use App\Models\Prenote\PrenoteMessage;
use Carbon\Carbon;
use Illuminate\Support\Facades\Event;
use PHPUnit\Framework\Attributes\DataProvider;

class PrenoteMessageControllerTest extends BaseApiTestCase
{
    public function setUp(): void
    {
        parent::setUp();
        Carbon::setTestNow(Carbon::now());
        Event::fake();
    }

    public function testUnauthorisedUsersCantSendPrenoteMessages()
    {
        $this->makeUnauthenticatedApiRequest(self::METHOD_POST, 'prenotes/messages')->assertUnauthorized();
    }

    public static function badCreateDataProvider(): array
    {
        return [
            'Missing callsign' => [
                [
                    'departure_airfield' => 'EGLL',
                    'departure_sid' => 'MODMI1G',
                    'destination_airfield' => 'EGJJ',
                    'requesting_controller_id' => 1,
                    'target_controller_id' => 2,
                    'expires_in_seconds' => 115,
                ],
            ],
            'Callsign not string' => [
                [
                    'callsign' => 123,
                    'departure_airfield' => 'EGLL',
                    'departure_sid' => 'MODMI1G',
                    'destination_airfield' => 'EGJJ',
                    'requesting_controller_id' => 1,
                    'target_controller_id' => 2,
                    'expires_in_seconds' => 115,
                ],
            ],
            'Departure airfield missing' => [
                [
                    'callsign' => 'BAW123',
                    'departure_sid' => 'MODMI1G',
                    'destination_airfield' => 'EGJJ',
                    'requesting_controller_id' => 1,
                    'target_controller_id' => 2,
                    'expires_in_seconds' => 115,
                ],
            ],
            'Departure airfield not string' => [
                [
                    'callsign' => 'BAW123',
                    'departure_airfield' => 123,
                    'departure_sid' => 'MODMI1G',
                    'destination_airfield' => 'EGJJ',
                    'requesting_controller_id' => 1,
                    'target_controller_id' => 2,
                    'expires_in_seconds' => 115,
                ],
            ],
            'Departure airfield not valid icao' => [
                [
                    'callsign' => 'BAW123',
                    'departure_airfield' => 'HEATHROW',
                    'departure_sid' => 'MODMI1G',
                    'destination_airfield' => 'EGJJ',
                    'requesting_controller_id' => 1,
                    'target_controller_id' => 2,
                    'expires_in_seconds' => 115,
                ],
            ],
            'Departure sid missing' => [
                [
                    'callsign' => 'BAW123',
                    'departure_airfield' => 'EGLL',
                    'destination_airfield' => 'EGJJ',
                    'requesting_controller_id' => 1,
                    'target_controller_id' => 2,
                    'expires_in_seconds' => 115,
                ],
            ],
            'Departure sid not string' => [
                [
                    'callsign' => 'BAW123',
                    'departure_airfield' => 'EGLL',
                    'departure_sid' => 123,
                    'destination_airfield' => 'EGJJ',
                    'requesting_controller_id' => 1,
                    'target_controller_id' => 2,
                    'expires_in_seconds' => 115,
                ],
            ],
            'Destination airfield missing' => [
                [
                    'callsign' => 'BAW123',
                    'departure_airfield' => 'EGLL',
                    'departure_sid' => 'MODMI1G',
                    'requesting_controller_id' => 1,
                    'target_controller_id' => 2,
                    'expires_in_seconds' => 115,
                ],
            ],
            'Destination airfield not string' => [
                [
                    'callsign' => 'BAW123',
                    'departure_airfield' => 'EGLL',
                    'departure_sid' => 'MODMI1G',
                    'destination_airfield' => 123,
                    'requesting_controller_id' => 1,
                    'target_controller_id' => 2,
                    'expires_in_seconds' => 115,
                ],
            ],
            'Destination airfield invalid icao' => [
                [
                    'callsign' => 'BAW123',
                    'departure_airfield' => 'EGLL',
                    'departure_sid' => 'MODMI1G',
                    'destination_airfield' => 'JERSEY',
                    'requesting_controller_id' => 1,
                    'target_controller_id' => 2,
                    'expires_in_seconds' => 115,
                ],
            ],
            'Requesting controller missing' => [
                [
                    'callsign' => 'BAW123',
                    'departure_airfield' => 'EGLL',
                    'departure_sid' => 'MODMI1G',
                    'destination_airfield' => 'EGJJ',
                    'target_controller_id' => 2,
                    'expires_in_seconds' => 115,
                ],
            ],
            'Requesting controller not integer' => [
                [
                    'callsign' => 'BAW123',
                    'departure_airfield' => 'EGLL',
                    'departure_sid' => 'MODMI1G',
                    'destination_airfield' => 'EGJJ',
                    'requesting_controller_id' => '123',
                    'target_controller_id' => 2,
                    'expires_in_seconds' => 115,
                ],
            ],
            'Requesting controller cant send prenotes' => [
                [
                    'callsign' => 'BAW123',
                    'departure_airfield' => 'EGLL',
                    'departure_sid' => 'MODMI1G',
                    'destination_airfield' => 'EGJJ',
                    'requesting_controller_id' => 4,
                    'target_controller_id' => 2,
                    'expires_in_seconds' => 115,
                ],
            ],
            'Target controller missing' => [
                [
                    'callsign' => 'BAW123',
                    'departure_airfield' => 'EGLL',
                    'departure_sid' => 'MODMI1G',
                    'destination_airfield' => 'EGJJ',
                    'requesting_controller_id' => 1,
                    'expires_in_seconds' => 115,
                ],
            ],
            'Target controller not integer' => [
                [
                    'callsign' => 'BAW123',
                    'departure_airfield' => 'EGLL',
                    'departure_sid' => 'MODMI1G',
                    'destination_airfield' => 'EGJJ',
                    'requesting_controller_id' => 1,
                    'target_controller_id' => 'abc',
                    'expires_in_seconds' => 115,
                ],
            ],
            'Target controller cant receive prenotes' => [
                [
                    'callsign' => 'BAW123',
                    'departure_airfield' => 'EGLL',
                    'departure_sid' => 'MODMI1G',
                    'destination_airfield' => 'EGJJ',
                    'requesting_controller_id' => 1,
                    'target_controller_id' => 4,
                    'expires_in_seconds' => 115,
                ],
            ],
            'Target and receiving controllers same' => [
                [
                    'callsign' => 'BAW123',
                    'departure_airfield' => 'EGLL',
                    'departure_sid' => 'MODMI1G',
                    'destination_airfield' => 'EGJJ',
                    'requesting_controller_id' => 2,
                    'target_controller_id' => 2,
                    'expires_in_seconds' => 115,
                ],
            ],
            'Expires in missing' => [
                [
                    'callsign' => 'BAW123',
                    'departure_airfield' => 'EGLL',
                    'departure_sid' => 'MODMI1G',
                    'destination_airfield' => 'EGJJ',
                    'requesting_controller_id' => 1,
                    'target_controller_id' => 2,
                ],
            ],
            'Expires is not integer' => [
                [
                    'callsign' => 'BAW123',
                    'departure_airfield' => 'EGLL',
                    'departure_sid' => 'MODMI1G',
                    'destination_airfield' => 'EGJJ',
                    'requesting_controller_id' => 1,
                    'target_controller_id' => 2,
                    'expires_in_seconds' => 'abc',
                ],
            ],
            'Expires in less than 1' => [
                [
                    'callsign' => 'BAW123',
                    'departure_airfield' => 'EGLL',
                    'departure_sid' => 'MODMI1G',
                    'destination_airfield' => 'EGJJ',
                    'requesting_controller_id' => 1,
                    'target_controller_id' => 2,
                    'expires_in_seconds' => 0,
                ],
            ],
        ];
    }

    #[DataProvider('badCreateDataProvider')]
    public function testItDoesntCreateAReleaseOnBadData(array $requestData)
    {
        $this->makeAuthenticatedApiRequest(self::METHOD_POST, 'prenotes/messages', $requestData)->assertStatus(422);

        $this->assertDatabaseCount('prenote_messages', 0);
    }

    public function testItDoesntCreateAPrenoteMessageIfOneAlreadyActiveForSameControler()
    {
        PrenoteMessage::create(
            [
                'callsign' => 'BAW123',
                'departure_airfield' => 'EGLL',
                'departure_sid' => 'MODMI1G',
                'destination_airfield' => 'EGJJ',
                'controller_position_id' => 1,
                'user_id' => self::ACTIVE_USER_CID,
                'target_controller_position_id' => 2,
                'expires_at' => Carbon::now()->addSeconds(15),
            ]
        );

        $requestData = [
            'callsign' => 'BAW123',
            'departure_airfield' => 'EGLL',
            'departure_sid' => 'MODMI1G',
            'destination_airfield' => 'EGJJ',
            'requesting_controller_id' => 1,
            'target_controller_id' => 2,
            'expires_in_seconds' => 115,
        ];

        $this->makeAuthenticatedApiRequest(self::METHOD_POST, 'prenotes/messages', $requestData)->assertStatus(422);
    }

    public function testItCreatesPrenoteMessages()
    {
        $requestData = [
            'callsign' => 'BAW123',
            'departure_airfield' => 'EGLL',
            'departure_sid' => 'MODMI1G',
            'destination_airfield' => 'EGJJ',
            'requesting_controller_id' => 1,
            'target_controller_id' => 2,
            'expires_in_seconds' => 115,
        ];

        $this->makeAuthenticatedApiRequest(self::METHOD_POST, 'prenotes/messages', $requestData)->assertCreated()
            ->assertJsonStructure(['id']);

        $this->assertDatabaseHas(
            'prenote_messages',
            [
                'callsign' => 'BAW123',
                'departure_airfield' => 'EGLL',
                'departure_sid' => 'MODMI1G',
                'destination_airfield' => 'EGJJ',
                'user_id' => self::ACTIVE_USER_CID,
                'controller_position_id' => 1,
                'target_controller_position_id' => 2,
                'expires_at' => Carbon::now()->addSeconds(115)->toDateTimeString(),
            ]
        );
    }

    public function testItCreatesPrenoteMessagesWithOptionalData()
    {
        $requestData = [
            'callsign' => 'BAW123',
            'departure_airfield' => 'EGLL',
            'departure_sid' => null,
            'destination_airfield' => null,
            'requesting_controller_id' => 1,
            'target_controller_id' => 2,
            'expires_in_seconds' => 115,
        ];

        $this->makeAuthenticatedApiRequest(self::METHOD_POST, 'prenotes/messages', $requestData)->assertCreated()
            ->assertJsonStructure(['id']);

        $this->assertDatabaseHas(
            'prenote_messages',
            [
                'callsign' => 'BAW123',
                'departure_airfield' => 'EGLL',
                'departure_sid' => null,
                'destination_airfield' => null,
                'user_id' => self::ACTIVE_USER_CID,
                'controller_position_id' => 1,
                'target_controller_position_id' => 2,
                'expires_at' => Carbon::now()->addSeconds(115)->toDateTimeString(),
            ]
        );
    }

    public function testItCreatesPrenoteMessageIfOneActiveForDifferentTargetController()
    {
        PrenoteMessage::create(
            [
                'callsign' => 'BAW123',
                'departure_airfield' => 'EGLL',
                'departure_sid' => 'MODMI1G',
                'destination_airfield' => 'EGJJ',
                'controller_position_id' => 1,
                'user_id' => self::ACTIVE_USER_CID,
                'target_controller_position_id' => 3,
                'expires_at' => Carbon::now()->addSeconds(15),
            ]
        );

        $requestData = [
            'callsign' => 'BAW123',
            'departure_airfield' => 'EGLL',
            'departure_sid' => 'MODMI1G',
            'destination_airfield' => 'EGJJ',
            'requesting_controller_id' => 1,
            'target_controller_id' => 2,
            'expires_in_seconds' => 115,
        ];

        $this->makeAuthenticatedApiRequest(self::METHOD_POST, 'prenotes/messages', $requestData)->assertCreated()
            ->assertJsonStructure(['id']);
    }

    private function getAcknowledgeRoute(int $messageId): string
    {
        return sprintf('prenotes/messages/%d/acknowledge', $messageId);
    }

    public function testUnauthorisedUsersCantAcknowledgePrenoteMessages()
    {
        $this->makeUnauthenticatedApiRequest(self::METHOD_PATCH, $this->getAcknowledgeRoute(123))
            ->assertUnauthorized();
    }

    public function testItAcknowledgesAPrenoteMessage()
    {
        $messageId = PrenoteMessage::create(
            [
                'callsign' => 'BAW123',
                'departure_airfield' => 'EGLL',
                'departure_sid' => 'MODMI1G',
                'destination_airfield' => 'EGJJ',
                'controller_position_id' => 1,
                'user_id' => self::ACTIVE_USER_CID,
                'target_controller_position_id' => 2,
                'expires_at' => Carbon::now()->addSeconds(115),
            ]
        )->id;

        $requestData = [
            'controller_position_id' => 2,
        ];

        $this->makeAuthenticatedApiRequest(self::METHOD_PATCH, $this->getAcknowledgeRoute($messageId), $requestData)
            ->assertNoContent();

        $this->assertDatabaseHas(
            'prenote_messages',
            [
                'id' => $messageId,
                'acknowledged_by' => self::ACTIVE_USER_CID,
            ]
        );
    }

    public function testItReturnsNotFoundIfNoMessageToAcknowledge()
    {
        $requestData = [
            'controller_position_id' => 2,
        ];

        $this->makeAuthenticatedApiRequest(self::METHOD_PATCH, $this->getAcknowledgeRoute(123), $requestData)
            ->assertNotFound()
            ->assertJson([]);
    }

    public function testItReturnsForbiddenIfNotAllowedToAcknowledgeMessage()
    {
        $messageId = PrenoteMessage::create(
            [
                'callsign' => 'BAW123',
                'departure_airfield' => 'EGLL',
                'departure_sid' => 'MODMI1G',
                'destination_airfield' => 'EGJJ',
                'controller_position_id' => 1,
                'user_id' => self::ACTIVE_USER_CID,
                'target_controller_position_id' => 2,
                'expires_at' => Carbon::now()->addSeconds(115),
            ]
        )->id;

        $requestData = [
            'controller_position_id' => 3,
        ];

        $this->makeAuthenticatedApiRequest(self::METHOD_PATCH, $this->getAcknowledgeRoute($messageId), $requestData)
            ->assertForbidden()
            ->assertJsonStructure(['message']);
    }

    public function testItReturnsConflictIfAlreadyAcknowledged()
    {
        $message = PrenoteMessage::create(
            [
                'callsign' => 'BAW123',
                'departure_airfield' => 'EGLL',
                'departure_sid' => 'MODMI1G',
                'destination_airfield' => 'EGJJ',
                'controller_position_id' => 1,
                'user_id' => self::ACTIVE_USER_CID,
                'target_controller_position_id' => 2,
                'expires_at' => Carbon::now()->addSeconds(115),
            ]
        );
        $message->acknowledge(self::ACTIVE_USER_CID);
        $messageId = $message->id;

        $requestData = [
            'controller_position_id' => 2,
        ];

        $this->makeAuthenticatedApiRequest(self::METHOD_PATCH, $this->getAcknowledgeRoute($messageId), $requestData)
            ->assertStatus(409)
            ->assertJsonStructure(['message']);
    }

    public static function badAcknowledgeDataProvider(): array
    {
        return [
            'Controller position id missing' => [
                [],
            ],
            'Controller position id not integer' => [
                [
                    'controller_position_id' => 'abc',
                ],
            ],
            'Controller position id doesnt exist integer' => [
                [
                    'controller_position_id' => 1234,
                ],
            ],
        ];
    }

    #[DataProvider('badAcknowledgeDataProvider')]
    public function testItDoesntAcknowledgeAReleaseOnBadData(array $requestData)
    {
        $messageId = PrenoteMessage::create(
            [
                'callsign' => 'BAW123',
                'departure_airfield' => 'EGLL',
                'departure_sid' => 'MODMI1G',
                'destination_airfield' => 'EGJJ',
                'controller_position_id' => 1,
                'user_id' => self::ACTIVE_USER_CID,
                'target_controller_position_id' => 2,
                'expires_at' => Carbon::now()->addSeconds(115),
            ]
        )->id;

        $this->makeAuthenticatedApiRequest(self::METHOD_PATCH, $this->getAcknowledgeRoute($messageId), $requestData)
            ->assertStatus(422);
    }

    private function getDeleteRoute(int $messageId): string
    {
        return sprintf('prenotes/messages/%d', $messageId);
    }

    public function testUnauthorisedUsersCantCancelPrenoteMessages()
    {
        $this->makeUnauthenticatedApiRequest(self::METHOD_DELETE, $this->getDeleteRoute(1))
            ->assertUnauthorized();
    }

    public function testItDeletesAPrenoteMessage()
    {
        $messageId = PrenoteMessage::create(
            [
                'callsign' => 'BAW123',
                'departure_airfield' => 'EGLL',
                'departure_sid' => 'MODMI1G',
                'destination_airfield' => 'EGJJ',
                'controller_position_id' => 1,
                'user_id' => self::ACTIVE_USER_CID,
                'target_controller_position_id' => 2,
                'expires_at' => Carbon::now()->addSeconds(115),
            ]
        )->id;

        $this->makeAuthenticatedApiRequest(self::METHOD_DELETE, $this->getDeleteRoute($messageId))
            ->assertNoContent();

        $this->assertSoftDeleted(
            'prenote_messages',
            [
                'id' => $messageId,
            ]
        );
    }

    public function testItReturnsNotFoundIfNoMessageToDelete()
    {
        $this->makeAuthenticatedApiRequest(self::METHOD_DELETE, $this->getDeleteRoute(123))
            ->assertNotFound()
            ->assertJson([]);
    }

    public function testItReturnsForbiddenIfNotAllowedToDeleteMessage()
    {
        $messageId = PrenoteMessage::create(
            [
                'callsign' => 'BAW123',
                'departure_airfield' => 'EGLL',
                'departure_sid' => 'MODMI1G',
                'destination_airfield' => 'EGJJ',
                'controller_position_id' => 1,
                'user_id' => self::BANNED_USER_CID,
                'target_controller_position_id' => 2,
                'expires_at' => Carbon::now()->addSeconds(115),
            ]
        )->id;

        $this->makeAuthenticatedApiRequest(self::METHOD_DELETE, $this->getDeleteRoute($messageId))
            ->assertForbidden()
            ->assertJsonStructure(['message']);
    }
}
