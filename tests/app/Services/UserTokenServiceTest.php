<?php

namespace App\Services;

use App\BaseApiTestCase;
use App\Exceptions\TooManyTokensException;
use App\Models\User\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Carbon\Carbon;
use TestingUtils\Traits\WithSeedUsers;

class UserTokenServiceTest extends BaseApiTestCase
{
    use WithSeedUsers;

    /**
     * Service under test
     *
     * @var UserTokenService
     */
    private $service;

    public function setUp()
    {
        parent::setUp();
        $this->service = $this->app->make(UserTokenService::class);
    }

    public function testItConstructs()
    {
        $this->assertInstanceOf(UserTokenService::class, $this->service);
    }

    public function testItThrowsAnExceptionIfUserNotFound()
    {
        $this->expectException(ModelNotFoundException::class);
        $this->service->create(999);
    }

    public function testItHasAMaximumNumberOfAllowedTokens()
    {
        // This user already has one token from the test setup
        for ($numTokens = 0; $numTokens < UserTokenService::MAXIMUM_ALLOWED_TOKENS - 1; $numTokens++) {
            $this->service->create(1203533);
        }

        $this->expectException(TooManyTokensException::class);
        $this->service->create(1203533);
    }

    public function testItCreatesAUserToken()
    {
        $token = $this->service->create(1203533);
        $this->get('/', ['Authorization' => 'Bearer ' . $token])->seeStatusCode(418);
    }

    public function testDeleteRemovesAToken()
    {
        $token = $this->activeUser()->tokens()->first()->id;
        $this->service->delete($token);

        $token = $this->activeUser()->tokens();
        $this->assertFalse($token->exists());
    }

    public function testDeleteThrowsAnExceptionIfNoTokenExists()
    {
        $this->expectException(ModelNotFoundException::class);
        $this->service->delete('notoken');
    }

    public function testDeleteAllTokensForUserDeletesTheirTokens()
    {
        $this->service->create(1203533);
        $this->service->create(1203533);

        $this->assertGreaterThan(0, User::findOrFail(1203533)->tokens->count());
        $this->service->deleteAllForUser(1203533);
        $this->assertEquals(0, User::findOrFail(1203533)->tokens->count());
    }

    public function testDeleteAllTokensForUserThrowsExceptionIfUserDoesNotExist()
    {
        $this->expectException(ModelNotFoundException::class);
        $this->service->deleteAllForUser(1);
    }
}
