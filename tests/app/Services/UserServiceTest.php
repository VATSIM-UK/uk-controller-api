<?php


namespace App\Services;

use App\BaseFunctionalTestCase;
use App\Exceptions\UserAlreadyExistsException;
use App\Models\User\UserStatus;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use TestingUtils\Traits\WithSeedUsers;

class UserServiceTest extends BaseFunctionalTestCase
{
    use WithSeedUsers;

    /**
     * Service under test
     *
     * @var UserService
     */
    private $service;

    public function setUp() : void
    {
        parent::setUp();
        $this->service = $this->app->make(UserService::class);
    }

    public function testItConstructs()
    {
        $this->assertInstanceOf(UserService::class, $this->service);
    }

    public function testItThrowsAnExceptionIfUserAlreadyExists()
    {
        $this->expectException(UserAlreadyExistsException::class);
        $this->expectExceptionMessage('User with VATSIM CID 1203533 already exists');
        $this->service->createUser(1203533);
    }

    public function testItCreatesANewActiveUser()
    {
        $this->service->createUser(1402313);
        $this->assertDatabaseHas('user', ['id' => 1402313, 'status' => UserStatus::ACTIVE]);
    }

    public function testItCreatesAnAccessToken()
    {
        $this->service->createUser(1402313);
        $this->assertDatabaseHas(
            'oauth_access_tokens',
            [
                'user_id' => 1402313,
                'client_id' => 1,
                'revoked' => 0,
            ]
        );
    }

    public function testCreatingAUserReturnsAConfig()
    {
        $actual = $this->service->createUser(1402313);

        $expectedApiUrl = config('app.url');
        $this->assertEquals($expectedApiUrl, $actual->apiUrl());
        $this->assertNotNull($actual->apiKey());
    }

    public function testTheCreatedTokenWorks()
    {
        $accessToken = $this->service->createUser(1402313)->apiKey();
        $this->maketestRequest('/', $accessToken);
    }

    public function testItCreatesAnAdminUser()
    {
        $this->service->createAdminUser();
        $this->assertDatabaseHas(
            'oauth_access_tokens',
            [
                'user_id' => 2,
                'client_id' => 1,
                'revoked' => 0,
            ]
        );
    }

    public function testItCreatesAdminUsersSequentially()
    {
        $this->service->createAdminUser();
        $this->service->createAdminUser();
        $this->assertDatabaseHas(
            'oauth_access_tokens',
            [
                'user_id' => 3,
                'client_id' => 1,
                'revoked' => 0,
            ]
        );
    }

    public function testItCreatesAnAdminAccessToken()
    {
        $accessToken = $this->service->createAdminUser();
        $this->maketestRequest('/useradmin', $accessToken);
    }

    public function testItAVersionAdminAccessToken()
    {
        $accessToken = $this->service->createAdminUser();
        $this->maketestRequest('/versionadmin', $accessToken);
    }

    public function testBanUserThrowsExceptionIfUserDoesntExist()
    {
        $this->expectException(ModelNotFoundException::class);
        $this->service->banUser(-999);
    }

    public function testBanUserBansTheUser()
    {
        $this->service->banUser(1203533);
        $this->assertTrue($this->activeUser()->accountStatus->banned);
    }

    public function testDisableUserThrowsExceptionIfUserDoesntExist()
    {
        $this->expectException(ModelNotFoundException::class);
        $this->service->disableUser(-999);
    }

    public function testDisableUserDisablesTheUser()
    {
        $this->service->disableUser(1203533);
        $this->assertTrue($this->activeUser()->accountStatus->disabled);
    }

    public function testReactivateUserThrowsExceptionIfUserDoesntExist()
    {
        $this->expectException(ModelNotFoundException::class);
        $this->service->reactivateUser(-999);
    }

    public function testReactivateUserActivatesTheUser()
    {
        $this->service->reactivateUser(1203534);
        $this->assertTrue($this->bannedUser()->accountStatus->active);
    }

    public function testItThrowsAnExceptionIfGettingANonExistantUser()
    {
        $this->expectException(ModelNotFoundException::class);
        $this->service->getUser(123);
    }

    public function testItFindsAUser()
    {
        $this->assertEquals(1203533, $this->service->getUser(1203533)->id);
    }

    private function maketestRequest(string $uri, string $token)
    {
        $this->get($uri, ['Authorization' => 'Bearer ' . $token])->assertStatus(418);
    }
}
