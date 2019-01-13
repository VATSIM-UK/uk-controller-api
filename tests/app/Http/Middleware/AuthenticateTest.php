<?php
namespace App\Http\Middleware;

use App\BaseApiTestCase;
use App\Models\User\User;
use App\Providers\AuthServiceProvider;
use TestingUtils\Traits\WithSeedUsers;

class AuthenticateTest extends BaseApiTestCase
{
    use WithSeedUsers;

    public function testItConstructs()
    {
        $this->assertInstanceOf(Authenticate::class, $this->app->make(Authenticate::class));
    }

    public function testItRejectsUsersWithNoKey()
    {
        $this->json('GET', '/')->assertResponseStatus(401);
    }

    public function testItRejectsUsersWithInvalidKey()
    {
        $this->json('GET', '/', [], ['Authorization' => 'nope'])
            ->assertResponseStatus(403);
    }

    public function testItAllowsActiveUsersWithValidKey()
    {
        $token = $this->activeUser()->createToken('access', [AuthServiceProvider::SCOPE_USER])->accessToken;
        $this->json(
            'GET',
            '/',
            [],
            ['Authorization' => 'Bearer ' . $token]
        )
            ->seeJson(
                [
                    'message' => 'Nothing here but us teapots...',
                ]
            )
            ->assertResponseStatus(418);
    }
}
