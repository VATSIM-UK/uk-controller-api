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
        $this->makeAuthenticatedApiRequest(self::METHOD_PUT, $this->getVersionUri(false), ['allowed' => true]);
        $this->assertDatabaseHas(
            'admin_log',
            [
                'user_id' => self::ACTIVE_USER_CID,
                'request_uri' => $this->getVersionUri(false),
                'request_body' => json_encode(['allowed' => true]),
            ]
        );
    }

    public function testItPassesOnTheRequestToTheNextMiddleware()
    {
        $this->makeAuthenticatedApiRequest(self::METHOD_PUT, $this->getVersionUri(false), ['allowed' => true]);
        $this->makeAuthenticatedApiRequest(self::METHOD_GET, $this->getVersionUri(true))->assertStatus(200);
    }

    private function getVersionUri(bool $withStatus): string
    {
        return '/version/99.99.99' . ($withStatus ? '/status' : '');
    }
}
