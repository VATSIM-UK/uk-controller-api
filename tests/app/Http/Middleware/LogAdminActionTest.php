<?php
namespace App\Http\Middleware;

use App\BaseApiTestCase;
use App\Providers\AuthServiceProvider;
use TestingUtils\Traits\WithSeedUsers;

class LogAdminActionTest extends BaseApiTestCase
{
    use WithSeedUsers;

    protected static $tokenScope = [
        AuthServiceProvider::SCOPE_VERSION_ADMIN,
    ];

    public function testItConstructs()
    {
        $this->assertInstanceOf(LogAdminAction::class, $this->app->make(LogAdminAction::class));
    }

    public function testItRecordsAnAdminEvent()
    {
        $this->makeAuthenticatedApiRequest(self::METHOD_PUT, '/version/99.99.99', ['allowed' => true]);
        $this->assertDatabaseHas(
            'admin_log',
            [
                'user_id' => self::ACTIVE_USER_CID,
                'request_uri' => '/version/99.99.99',
                'request_body' => json_encode(['allowed' => true]),
            ]
        );
    }

    public function testItPassesOnTheRequestToTheNextMiddleware()
    {
        $this->makeAuthenticatedApiRequest(self::METHOD_PUT, '/version/99.99.99', ['allowed' => true]);
        $this->makeAuthenticatedApiRequest(self::METHOD_GET, '/version/99.99.99/status')->assertStatus(200);
    }
}
