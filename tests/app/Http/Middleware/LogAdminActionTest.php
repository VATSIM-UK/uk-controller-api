<?php

namespace App\Http\Middleware;

use App\BaseApiTestCase;

use App\Models\User\UserStatus;
use App\Providers\AuthServiceProvider;
use TestingUtils\Traits\WithSeedUsers;

class LogAdminActionTest extends BaseApiTestCase
{
    use WithSeedUsers;

    protected static $tokenScope = [
        AuthServiceProvider::SCOPE_USER_ADMIN
    ];

    public function testItConstructs()
    {
        $this->assertInstanceOf(LogAdminAction::class, $this->app->make(LogAdminAction::class));
    }

    public function testItRecordsAnAdminEvent()
    {
        // Create a new user
        $this->makeAuthenticatedApiRequest(
            self::METHOD_POST,
            'api/user',
            ['cid' => 1203539]
        )->assertCreated();

        // Check the user was created
        $this->assertDatabaseHas(
            'user',
            [
                'id' => 1203539,
                'status' => UserStatus::ACTIVE,
            ]
        );

        // Check we have a log entry
        $this->assertDatabaseHas(
            'admin_log',
            [
                'user_id' => self::ACTIVE_USER_CID,
                'request_uri' => "/api/user",
                'request_body' => json_encode(['cid' => 1203539]),
            ]
        );
    }
}
