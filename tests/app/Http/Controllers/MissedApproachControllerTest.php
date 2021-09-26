<?php

namespace App\Http\Controllers;

use App\BaseApiTestCase;
use App\Models\MissedApproach\MissedApproachNotification;
use Carbon\Carbon;

class MissedApproachControllerTest extends BaseApiTestCase
{
    public function setUp(): void
    {
        parent::setUp();
        $this->withoutEvents();
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
        MissedApproachNotification::create(
            ['callsign' => 'BAW123', 'user_id' => self::ACTIVE_USER_CID, 'expires_at' => Carbon::now()->addMinute()]
        );

        $this->makeUnauthenticatedApiRequest(self::METHOD_POST, 'missed-approaches', ['callsign' => 'BAW123'])
            ->assertUnauthorized();
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
