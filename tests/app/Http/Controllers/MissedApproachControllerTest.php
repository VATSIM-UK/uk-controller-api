<?php

namespace App\Http\Controllers;

use App\BaseApiTestCase;
use App\Models\MissedApproach\MissedApproachNotification;
use Carbon\Carbon;
use Illuminate\Support\Facades\Event;
use PHPUnit\Framework\Attributes\DataProvider;
use util\Traits\WithNetworkController;

class MissedApproachControllerTest extends BaseApiTestCase
{
    use WithNetworkController;

    public function setUp(): void
    {
        parent::setUp();
        Event::fake();
        $this->setNetworkController(self::ACTIVE_USER_CID);
    }

    public function testItCreatesAMissedApproach()
    {
        $this->makeAuthenticatedApiRequest(self::METHOD_POST, 'missed-approaches', ['callsign' => 'BAW123'])
            ->assertCreated()
            ->assertJsonStructure(['id', 'expires_at']);

        $this->assertDatabaseHas(
            'missed_approach_notifications',
            [
                'callsign' => 'BAW123',
            ]
        );
    }

    public function testItReturnsConflictIfApproachAlreadyActive()
    {
        MissedApproachNotification::create(
            ['callsign' => 'BAW123', 'user_id' => self::ACTIVE_USER_CID, 'expires_at' => Carbon::now()->addMinute()]
        );

        $this->makeAuthenticatedApiRequest(self::METHOD_POST, 'missed-approaches', ['callsign' => 'BAW123'])
            ->assertJsonStructure(['message'])
            ->assertStatus(409);
    }

    public function testItWontAllowUnauthenticatedUsersToCreateAMissedApproach()
    {
        $this->makeUnauthenticatedApiRequest(self::METHOD_POST, 'missed-approaches', ['callsign' => 'BAW123'])
            ->assertUnauthorized();
    }

    public function testItWontAllowUsersNotControllingToTrigger()
    {
        $this->setNetworkControllerUnrecognisedPosition(self::ACTIVE_USER_CID);
        $this->makeAuthenticatedApiRequest(self::METHOD_POST, 'missed-approaches', ['callsign' => 'BAW123'])
            ->assertForbidden();
    }

    public function testItWontAllowUsersNotLoggedInToTrigger()
    {
        $this->logoutNetworkController(self::ACTIVE_USER_CID);
        $this->makeAuthenticatedApiRequest(self::METHOD_POST, 'missed-approaches', ['callsign' => 'BAW123'])
            ->assertForbidden();
    }

    #[DataProvider('badDataProvider')]
    public function testItReturnsBadOnBadData(array $requestData)
    {
        $this->makeAuthenticatedApiRequest(self::METHOD_POST, 'missed-approaches', $requestData)
            ->assertStatus(422);
    }

    public static function badDataProvider(): array
    {
        return [
            'Callsign not a string' => [
                ['callsign' => 123]
            ],
            'Callsign missing' => [
                ['notcallsign' => 'BAW123']
            ],
        ];
    }

    public function testItAcknowledgesAMissedApproach()
    {
        $missed = MissedApproachNotification::create(
            ['callsign' => 'BAW123', 'user_id' => self::ACTIVE_USER_CID, 'expires_at' => Carbon::now()->addMinute()]
        );

        $this->makeAuthenticatedApiRequest(
            self::METHOD_PATCH,
            'missed-approaches/' . $missed->id,
            ['remarks' => 'Some remarks']
        )
            ->assertOk();

        $missed->refresh();
        $this->assertNotNull($missed->acknowledged_at);
        $this->assertEquals('Some remarks', $missed->remarks);
    }

    public function testItReturnsForbiddenIfUserCannotPerformAction()
    {
        $missed = MissedApproachNotification::create(
            ['callsign' => 'BAW123', 'user_id' => self::ACTIVE_USER_CID, 'expires_at' => Carbon::now()->addMinute()]
        );

        // LON_C_CTR, can't acknowledge this
        $this->setNetworkController(self::ACTIVE_USER_CID, 4);

        $this->makeAuthenticatedApiRequest(
            self::METHOD_PATCH,
            'missed-approaches/' . $missed->id,
            ['remarks' => 'Some remarks']
        )
            ->assertForbidden()
            ->assertJsonStructure(['message']);

        $missed->refresh();
        $this->assertNull($missed->acknowledged_at);
    }

    #[DataProvider('badDataAcknowledgeProvider')]
    public function testItReturnsBadOnBadAcknowledgementData(array $requestData)
    {
        $missed = MissedApproachNotification::create(
            ['callsign' => 'BAW123', 'user_id' => self::ACTIVE_USER_CID, 'expires_at' => Carbon::now()->addMinute()]
        );

        $this->makeAuthenticatedApiRequest(self::METHOD_PATCH, 'missed-approaches/' . $missed->id, $requestData)
            ->assertUnprocessable();
    }

    public static function badDataAcknowledgeProvider(): array
    {
        return [
            'Remarks not a string' => [
                ['remarks' => 123]
            ],
            'Remarks missing' => [
                ['nottremarks' => 'remarks']
            ],
        ];
    }

    public function testItWontAllowUnauthenticatedUsersToAcknowledgeAMissedApproach()
    {
        $missed = MissedApproachNotification::create(
            ['callsign' => 'BAW123', 'user_id' => self::ACTIVE_USER_CID, 'expires_at' => Carbon::now()->addMinute()]
        );
        $this->makeUnauthenticatedApiRequest(
            self::METHOD_PATCH,
            'missed-approaches/' . $missed->id,
            ['callsign' => 'BAW123']
        )
            ->assertUnauthorized();
    }

    public function testItWontAllowUsersNotControllingToAcknowledge()
    {
        $missed = MissedApproachNotification::create(
            ['callsign' => 'BAW123', 'user_id' => self::ACTIVE_USER_CID, 'expires_at' => Carbon::now()->addMinute()]
        );

        $this->setNetworkControllerUnrecognisedPosition(self::ACTIVE_USER_CID);
        $this->makeAuthenticatedApiRequest(
            self::METHOD_PATCH,
            'missed-approaches/' . $missed->id,
            ['callsign' => 'BAW123']
        )
            ->assertForbidden();
    }

    public function testItWontAllowUsersNotLoggedInToAcknowledge()
    {
        $missed = MissedApproachNotification::create(
            ['callsign' => 'BAW123', 'user_id' => self::ACTIVE_USER_CID, 'expires_at' => Carbon::now()->addMinute()]
        );

        $this->logoutNetworkController(self::ACTIVE_USER_CID);
        $this->makeAuthenticatedApiRequest(
            self::METHOD_PATCH,
            'missed-approaches/' . $missed->id,
            ['callsign' => 'BAW123']
        )
            ->assertForbidden();
    }

    public function testItReturnsNotFoundAcknowledgingNonExistentMissedApproach()
    {
        $this->makeAuthenticatedApiRequest(self::METHOD_PATCH, 'missed-approaches/9999', ['callsign' => 'BAW123'])
            ->assertNotFound();
    }
}
