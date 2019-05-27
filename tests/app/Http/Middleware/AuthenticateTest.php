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
        $this->json('GET', '/')->assertStatus(401);
    }

    public function testItRejectsUsersWithInvalidKey()
    {
        $this->json('GET', '/', [], ['Authorization' => 'nope'])
            ->assertStatus(403);
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
            ->assertJson(
                [
                    'message' => 'Nothing here but us teapots...',
                ]
            )
            ->assertStatus(418);
    }
}
