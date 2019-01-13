<?php

namespace App\Http\Controllers;

use App\BaseApiTestCase;
use App\Models\User\User;
use App\Providers\AuthServiceProvider;
use App\Services\UserTokenService;

class UserControllerTest extends BaseApiTestCase
{
    protected static $tokenScope = [
        AuthServiceProvider::SCOPE_USER_ADMIN,
    ];

    protected static $tokenUser = 0;

    public function testItConstructs()
    {
        $controller = $this->app->make(UserController::class);
        $this->assertInstanceOf(UserController::class, $controller);
    }

    public function testItRequiresUserAdminScope()
    {
        $this->regenerateAccessToken([], static::$tokenUser);
        $this->makeAuthenticatedApiRequest(self::METHOD_POST, 'user/1203532')->seeStatusCode(403);
    }

    public function testCreateUserReturnsTheCorrectJsonStructure()
    {
        $this->makeAuthenticatedApiRequest(self::METHOD_POST, 'user/1203532')->seeJsonStructure(
            [
                'api-url',
                'api-key',
            ]
        );
    }

    public function testCreateUserReturnsCreatedOnSuccess()
    {
        $this->makeAuthenticatedApiRequest(self::METHOD_POST, 'user/1203532')->seeStatusCode(201);
    }

    public function testCreateUserReturnsUnprocessableOnAlreadyExists()
    {
        $this->makeAuthenticatedApiRequest(self::METHOD_POST, 'user/1203532')->seeStatusCode(201);
        $this->makeAuthenticatedApiRequest(self::METHOD_POST, 'user/1203532')->seeStatusCode(422);
    }

    public function testItReturnsNoContentOnBanningUser()
    {
        $this->makeAuthenticatedApiRequest(self::METHOD_PUT, 'user/1203533/ban')->seeStatusCode(204);
    }

    public function testItReturnsNotFoundWhenNoUserToBan()
    {
        $this->makeAuthenticatedApiRequest(self::METHOD_PUT, 'user/800000/ban')->seeStatusCode(404);
    }

    public function testItReturnsNoContentOnDisablingUser()
    {
        $this->makeAuthenticatedApiRequest(self::METHOD_PUT, 'user/1203533/disable')->seeStatusCode(204);
    }

    public function testItReturnsNotFoundWhenNoUserToDisable()
    {
        $this->makeAuthenticatedApiRequest(self::METHOD_PUT, 'user/800000/disable')->seeStatusCode(404);
    }

    public function testItReturnsNoContentOnReactivatingUser()
    {
        $this->makeAuthenticatedApiRequest(self::METHOD_PUT, 'user/1203535/reactivate')->seeStatusCode(204);
    }

    public function testItReturnsNotFoundWhenNoUserToReactivate()
    {
        $this->makeAuthenticatedApiRequest(self::METHOD_PUT, 'user/800000/reactivate')->seeStatusCode(404);
    }

    public function testItReturnsAUser()
    {
        $this->makeAuthenticatedApiRequest(self::METHOD_GET, 'user/1203533')
        ->seeJsonStructure(
            [
                'id',
                'status',
                'tokens',
            ]
        )
        ->seeStatusCode(200);
    }

    public function testItReturns404OnUserNotFound()
    {
        $this->makeAuthenticatedApiRequest(self::METHOD_GET, 'user/1500000')
            ->seeStatusCode(404);
    }

    public function testItReturnsAUserToken()
    {
        $this->makeAuthenticatedApiRequest(self::METHOD_POST, 'user/1203533/token')
            ->seeJsonStructure(
                [
                    'api-key',
                    'api-url',
                ]
            )
            ->seeStatusCode(201);
    }

    public function testItReturnsNotFoundIfCreatingTokenForUnknownUser()
    {
        $this->makeAuthenticatedApiRequest(self::METHOD_POST, 'user/1500000/token')
            ->seeStatusCode(404);
    }

    public function testItReturnsUnprocessableIfCreatingTooManyTokens()
    {
        for ($i = 0; $i < UserTokenService::MAXIMUM_ALLOWED_TOKENS; $i++) {
            $this->makeAuthenticatedApiRequest(self::METHOD_POST, 'user/1203533/token')
                ->seeStatusCode(201);
        }

        $this->makeAuthenticatedApiRequest(self::METHOD_POST, 'user/1203533/token')
            ->seeStatusCode(422);
    }

    public function testItReturnsNoContentOnSuccessfulTokenDeletion()
    {
        $tokenId = User::findOrFail(1203533)->createToken('access', [AuthServiceProvider::SCOPE_USER])->token->id;
        $this->makeAuthenticatedApiRequest(self::METHOD_DELETE, 'token/' . $tokenId)
            ->seeStatusCode(204);
    }

    public function testItReturnsNotFoundOnNoTokenToDelete()
    {
        $this->makeAuthenticatedApiRequest(self::METHOD_DELETE, 'token/abc')
            ->seeStatusCode(404);
    }
}
