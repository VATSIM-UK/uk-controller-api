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
            '/api/authorise',
            [],
            ['Authorization' => 'Bearer ' . $token]
        )
            ->assertJson(
                [
                    'message' => UserIsDisabled::FAILURE_MESSAGE,
                ]
            )
            ->assertStatus(403);
    }

    public function testItAllowsActiveUsers()
    {
        $token = $this->activeUser()->createToken('access', [AuthServiceProvider::SCOPE_USER])->accessToken;
        $this->json(
            'GET',
            '/api/authorise',
            [],
            ['Authorization' => 'Bearer ' . $token]
        )
            ->assertJson(
                [
                    'message' => 'Nothing here but us teapots...',
                ]
            )
            ->assertStatus(200);
    }
}
