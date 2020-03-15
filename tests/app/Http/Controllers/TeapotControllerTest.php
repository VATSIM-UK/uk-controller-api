<?php
namespace App\Http\Controllers;

use App\BaseApiTestCase;
use App\Models\User\User;
use Carbon\Carbon;
use TestingUtils\Traits\WithSeedUsers;

class TeapotControllerTest extends BaseApiTestCase
{
    use WithSeedUsers;

    public function testItConstructs()
    {
        $controller = new TeapotController();
        $this->assertInstanceOf(TeapotController::class, $controller);
    }

    public function testItAcceptsGet()
    {
        $this->makeAuthenticatedApiRequest(self::METHOD_GET, '/')
            ->assertJson(
                [
                    'message' => 'Nothing here but us teapots...',
                ]
            )
            ->assertStatus(418);
    }

    public function testItAcceptsGetAndReturns200()
    {
        $this->makeAuthenticatedApiRequest(self::METHOD_GET, '/authorise')
            ->assertJson(
                [
                    'message' => 'Nothing here but us teapots...',
                ]
            )
            ->assertStatus(200);
    }

    public function testGetAssignmentRejectsTokensWithoutUserScope()
    {
        $this->regenerateAccessToken([], static::$tokenUser);
        $this->makeAuthenticatedApiRequest(self::METHOD_GET, '/')
            ->assertStatus(403);
    }

    public function testItSetsUsersLastLogin()
    {
        Carbon::setTestNow(Carbon::now());
        $this->makeAuthenticatedApiRequest(self::METHOD_GET, '/');
        $this->assertEquals($this->activeUser()->last_login, Carbon::now());
    }

    public function testItDoesntAcceptPut()
    {
        $this->makeAuthenticatedApiRequest(self::METHOD_PUT, '/')->assertStatus(405);
    }

    public function testItDoesntAcceptPatch()
    {
        $this->makeAuthenticatedApiRequest(self::METHOD_PATCH, '/')->assertStatus(405);
    }

    public function testItDoesntAcceptDelete()
    {
        $this->makeAuthenticatedApiRequest(self::METHOD_DELETE, '/')->assertStatus(405);
    }
}
