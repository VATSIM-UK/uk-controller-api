<?php

namespace App\Http\Middleware;

use App\BaseApiTestCase;
use App\Models\User\User;
use App\Providers\AuthServiceProvider;
use TestingUtils\Traits\WithSeedUsers;

class UserIsBannedTest extends BaseApiTestCase
{
    use WithSeedUsers;

    public function testItConstructs()
    {
        $middleware = new UserIsBanned();
        $this->assertInstanceOf(UserIsBanned::class, $middleware);
    }

    public function testItRejectsBannedUsers()
    {
        $token = $this->bannedUser()->createToken('access')->accessToken;

        $this->json(
            'GET',
            '/',
            [],
            ['Authorization' => 'Bearer ' . $token]
        )
            ->seeJson(
                [
                    'message' => UserIsBanned::FAILURE_MESSAGE,
                ]
            )
            ->assertResponseStatus(403);
    }

    public function testItAllowsUnbannedUsers()
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
