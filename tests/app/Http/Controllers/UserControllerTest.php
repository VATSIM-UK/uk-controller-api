<?php

namespace App\Http\Controllers;

use App\BaseApiTestCase;
use App\Models\User\Admin;
use App\Models\User\User;
use App\Providers\AuthServiceProvider;
use App\Services\UserTokenService;

class UserControllerTest extends BaseApiTestCase
{
    protected static $tokenScope = [
        AuthServiceProvider::SCOPE_USER_ADMIN,
    ];

    protected static $tokenUser = 1;

    public function testItConstructs()
    {
        $controller = $this->app->make(UserController::class);
        $this->assertInstanceOf(UserController::class, $controller);
    }

    public function testItRequiresUserAdminScope()
    {
        $this->regenerateAccessToken([], static::$tokenUser);
        $this->makeAuthenticatedApiRequest(self::METHOD_POST, 'user/1203532')->assertStatus(403);
    }

    public function testCreateUserReturnsTheCorrectJsonStructure()
    {
        $this->makeAuthenticatedApiRequest(self::METHOD_POST, 'user/1203532')->assertJsonStructure(
            [
                'api-url',
                'api-key',
            ]
        );
    }

    public function testCreateUserReturnsCreatedOnSuccess()
    {
        $this->makeAuthenticatedApiRequest(self::METHOD_POST, 'user/1203532')->assertStatus(201);
    }

    public function testCreateUserReturnsUnprocessableOnAlreadyExists()
    {
        $this->makeAuthenticatedApiRequest(self::METHOD_POST, 'user/1203532')->assertStatus(201);
        $this->makeAuthenticatedApiRequest(self::METHOD_POST, 'user/1203532')->assertStatus(422);
    }

    public function testItReturnsNoContentOnBanningUser()
    {
        $this->makeAuthenticatedApiRequest(self::METHOD_PUT, 'user/1203533/ban')->assertStatus(204);
    }

    public function testItReturnsNotFoundWhenNoUserToBan()
    {
        $this->makeAuthenticatedApiRequest(self::METHOD_PUT, 'user/800000/ban')->assertStatus(404);
    }

    public function testItReturnsNoContentOnDisablingUser()
    {
        $this->makeAuthenticatedApiRequest(self::METHOD_PUT, 'user/1203533/disable')->assertStatus(204);
    }

    public function testItReturnsNotFoundWhenNoUserToDisable()
    {
        $this->makeAuthenticatedApiRequest(self::METHOD_PUT, 'user/800000/disable')->assertStatus(404);
    }

    public function testItReturnsNoContentOnReactivatingUser()
    {
        $this->makeAuthenticatedApiRequest(self::METHOD_PUT, 'user/1203535/reactivate')->assertStatus(204);
    }

    public function testItReturnsNotFoundWhenNoUserToReactivate()
    {
        $this->makeAuthenticatedApiRequest(self::METHOD_PUT, 'user/800000/reactivate')->assertStatus(404);
    }

    public function testItReturnsAUser()
    {
        $this->makeAuthenticatedApiRequest(self::METHOD_GET, 'user/1203533')
            ->assertJsonStructure(
                [
                    'id',
                    'status',
                    'tokens',
                ]
            )
            ->assertStatus(200);
    }

    public function testItReturns404OnUserNotFound()
    {
        $this->makeAuthenticatedApiRequest(self::METHOD_GET, 'user/1500000')
            ->assertStatus(404);
    }

    public function testItReturnsAUserToken()
    {
        $this->makeAuthenticatedApiRequest(self::METHOD_POST, 'user/1203533/token')
            ->assertJsonStructure(
                [
                    'api-key',
                    'api-url',
                ]
            )
            ->assertStatus(201);
    }

    public function testItReturnsNotFoundIfCreatingTokenForUnknownUser()
    {
        $this->makeAuthenticatedApiRequest(self::METHOD_POST, 'user/1500000/token')
            ->assertStatus(404);
    }

    public function testItReturnsUnprocessableIfCreatingTooManyTokens()
    {
        for ($i = 0; $i < UserTokenService::MAXIMUM_ALLOWED_TOKENS; $i++) {
            $this->makeAuthenticatedApiRequest(self::METHOD_POST, 'user/1203533/token')
                ->assertStatus(201);
        }

        $this->makeAuthenticatedApiRequest(self::METHOD_POST, 'user/1203533/token')
            ->assertStatus(422);
    }

    public function testItReturnsNoContentOnSuccessfulTokenDeletion()
    {
        $tokenId = User::findOrFail(1203533)->createToken('access', [AuthServiceProvider::SCOPE_USER])->token->id;
        $this->makeAuthenticatedApiRequest(self::METHOD_DELETE, 'token/' . $tokenId)
            ->assertStatus(204);
    }

    public function testItReturnsNotFoundOnNoTokenToDelete()
    {
        $this->makeAuthenticatedApiRequest(self::METHOD_DELETE, 'token/abc')
            ->assertStatus(404);
    }

    public function testItRejectsAdminLoginIfUserNotAdmin()
    {
        Admin::find(\UserTableSeeder::ACTIVE_USER_CID)->delete();
        $this->makeUnauthenticatedApiRequest(
            self::METHOD_POST,
            'admin/login',
            ['email' => 'ukcp@vatsim.uk', 'password' => 'letmein']
        )
            ->assertStatus(403);
    }

    public function testItRejectsAdminLoginIfAdminPasswordWrong()
    {
        $this->makeUnauthenticatedApiRequest(
            self::METHOD_POST,
            'admin/login',
            ['email' => 'ukcp@vatsim.uk', 'password' => 'dontletmein']
        )
            ->assertStatus(403);
    }

    public function testItRejectsAdminLoginIfNoEmail()
    {
        $this->makeUnauthenticatedApiRequest(
            self::METHOD_POST,
            'admin/login',
            ['emailnot' => 'ukcp@vatsim.uk', 'password' => 'dontletmein']
        )
            ->assertStatus(403);
    }

    public function testItIssuesAnAdminTokenIfPasswordCorrect()
    {
        $this->makeUnauthenticatedApiRequest(
            self::METHOD_POST,
            'admin/login',
            ['email' => 'ukcp@vatsim.uk', 'password' => 'letmein']
        )
            ->assertStatus(201)
            ->assertJsonStructure(['access_token', 'expires_at']);
    }
}
