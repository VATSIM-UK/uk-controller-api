<?php

namespace App\Http\Controllers;

use App\BaseApiTestCase;
use App\Models\MissedApproach\MissedApproachNotification;
use Carbon\Carbon;
use util\Traits\WithNetworkController;

class MissedApproachControllerTest extends BaseApiTestCase
{
    use WithNetworkController;

    public function setUp(): void
    {
        parent::setUp();
        $this->withoutEvents();
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

    /**
     * @dataProvider badDataProvider
     */
    public function testItReturnsBadOnBadData(array $requestData)
    {
        MissedApproachNotification::create(
            ['callsign' => 'BAW123', 'user_id' => self::ACTIVE_USER_CID, 'expires_at' => Carbon::now()->addMinute()]
        );

        $this->makeUnauthenticatedApiRequest(self::METHOD_POST, 'missed-approaches', $requestData)
            ->assertUnauthorized();
    }

    public function badDataProvider(): array
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
}
