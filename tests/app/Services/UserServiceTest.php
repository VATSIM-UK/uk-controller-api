<?php


namespace App\Services;

use App\BaseFunctionalTestCase;
use App\Exceptions\UserAlreadyExistsException;
use App\Models\User\UserStatus;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Laravel\Passport\PersonalAccessClient;
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

    private readonly int $personalAccessClientId;

    public function setUp() : void
    {
        parent::setUp();
        $this->service = $this->app->make(UserService::class);
        $this->personalAccessClientId = PersonalAccessClient::latest()->firstOrFail()->client_id;
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

    public function testItThrowsAnExceptionWithConfigIfUserAlreadyExists()
    {
        $this->expectException(UserAlreadyExistsException::class);
        $this->expectExceptionMessage('User with VATSIM CID 1203533 already exists');
        $this->service->createUserWithConfig(1203533);
    }

    public function testItCreatesANewActiveUserWithConfig()
    {
        $this->service->createUserWithConfig(1402313);
        $this->assertDatabaseHas('user', ['id' => 1402313, 'status' => UserStatus::ACTIVE]);
    }

    public function testItCreatesAnAccessTokenWithConfig()
    {
        $this->service->createUserWithConfig(1402313);
        $this->assertDatabaseHas(
            'oauth_access_tokens',
            [
                'user_id' => 1402313,
                'client_id' => $this->personalAccessClientId,
                'revoked' => 0,
            ]
        );
    }

    public function testCreatingAUserReturnsAConfig()
    {
        $actual = $this->service->createUserWithConfig(1402313);

        $expectedApiUrl = config('app.url');
        $this->assertEquals($expectedApiUrl, $actual->apiUrl());
        $this->assertNotNull($actual->apiKey());
    }

    public function testTheCreatedTokenWorks()
    {
        $accessToken = $this->service->createUserWithConfig(1402313)->apiKey();
        $this->makeTestRequest('/authorise', $accessToken);
    }

    public function testItCreatesAnAdminUser()
    {
        $this->service->createAdminUser();
        $this->assertDatabaseHas(
            'oauth_access_tokens',
            [
                'user_id' => 2,
                'client_id' => $this->personalAccessClientId,
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
                'client_id' => $this->personalAccessClientId,
                'revoked' => 0,
            ]
        );
    }

    public function testItCreatesAnAdminAccessToken()
    {
        $accessToken = $this->service->createAdminUser();
        $this->makeTestRequest('/useradmin', $accessToken);
    }

    public function testItCreateaDataAdminAccessToken()
    {
        $accessToken = $this->service->createDataAdminUser();
        $this->makeTestRequest('/dataadmin', $accessToken);
    }

    public function testItAVersionAdminAccessToken()
    {
        $accessToken = $this->service->createAdminUser();
        $this->makeTestRequest('/versionadmin', $accessToken);
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

    private function makeTestRequest(string $uri, string $token)
    {
        $this->get('api' . $uri, ['Authorization' => 'Bearer ' . $token])->assertStatus(200);
    }
}
