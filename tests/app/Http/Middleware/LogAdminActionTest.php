<?php

namespace App\Http\Middleware;

use App\BaseApiTestCase;
use App\Models\Aircraft\WakeCategory;
use App\Models\Airfield\Airfield;
use App\Models\Airfield\Terminal;
use App\Models\Stand\StandType;
use App\Providers\AuthServiceProvider;
use TestingUtils\Traits\WithSeedUsers;

class LogAdminActionTest extends BaseApiTestCase
{
    use WithSeedUsers;

    protected static $tokenScope = [
        AuthServiceProvider::SCOPE_DATA_ADMIN
    ];

    public function testItConstructs()
    {
        $this->assertInstanceOf(LogAdminAction::class, $this->app->make(LogAdminAction::class));
    }

    public function testItRecordsAnAdminEvent()
    {
        $navaidData = [
            'identifier' => 'OHAI',
            'latitude' => 1,
            'longitude' => 2,
        ];

        $this->makeAuthenticatedApiRequest(
            self::METHOD_POST,
            'api/admin/navaids',
            $navaidData
        )->assertCreated();

        $this->assertDatabaseHas(
            'admin_log',
            [
                'user_id' => self::ACTIVE_USER_CID,
                'request_uri' => "/api/admin/navaids",
                'request_body' => json_encode($navaidData),
            ]
        );
    }
}
