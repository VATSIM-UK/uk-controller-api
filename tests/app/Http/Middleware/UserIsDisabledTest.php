<?php

namespace App\Http\Middleware;

use App\BaseApiTestCase;
use App\Models\User\User;
use App\Providers\AuthServiceProvider;
use TestingUtils\Traits\WithSeedUsers;

class UserIsDisabledTest extends BaseApiTestCase
{
    use WithSeedUsers;

    public function testItConstructs()
    {
        $middleware = new UserIsDisabled();
        $this->assertInstanceOf(UserIsDisabled::class, $middleware);
    }

    public function testItRejectsDisabledUsers()
    {
        $token = $this->disabledUser()->createToken('access')->accessToken;
        $this->json(
            'GET',
            '/',
            [],
            ['Authorization' => 'Bearer ' . $token]
        )
            ->seeJson(
                [
                    'message' => UserIsDisabled::FAILURE_MESSAGE,
                ]
            )
            ->assertResponseStatus(403);
    }

    public function testItAllowsActiveUsers()
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
