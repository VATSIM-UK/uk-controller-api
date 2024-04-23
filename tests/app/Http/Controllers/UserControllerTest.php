<?php

namespace App\Http\Controllers;

use App\BaseApiTestCase;
use App\Models\Notification\Notification;
use App\Models\User\Admin;
use App\Models\User\User;
use App\Models\User\UserStatus;
use App\Providers\AuthServiceProvider;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class UserControllerTest extends BaseApiTestCase
{
    const USER_CREATE_URI = 'user/1203532';
    const USER_CREATE_NO_CONFIG_URI = 'user';
    const ADMIN_LOGIN_URI = 'admin/login';
    const ADMIN_EMAIL = 'ukcp@vatsim.uk';

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
        $this->makeAuthenticatedApiRequest(self::METHOD_POST, self::USER_CREATE_NO_CONFIG_URI, ['cid' => 800001])->assertStatus(403);
    }

    public function testCreateUserReturnsCreatedOnSuccess()
    {
        $this->makeAuthenticatedApiRequest(self::METHOD_POST, self::USER_CREATE_NO_CONFIG_URI, ['cid' => 800001])->assertStatus(201);
    }

    public function testCreateUserReturnsUnprocessableOnInvalidCid()
    {
        $this->makeAuthenticatedApiRequest(self::METHOD_POST, self::USER_CREATE_NO_CONFIG_URI, ['cid' => 'abc'])->assertUnprocessable();
    }

    public function testCreateUserReturnsUnprocessableOnNoCid()
    {
        $this->makeAuthenticatedApiRequest(self::METHOD_POST, self::USER_CREATE_NO_CONFIG_URI, [])->assertUnprocessable();
    }

    public function testCreateUserReturnsUnprocessableOnAlreadyExists()
    {
        $this->makeAuthenticatedApiRequest(self::METHOD_POST, self::USER_CREATE_NO_CONFIG_URI, ['cid' => 800001])->assertStatus(201);
        $this->makeAuthenticatedApiRequest(self::METHOD_POST, self::USER_CREATE_NO_CONFIG_URI, ['cid' => 800001])->assertStatus(422);
    }

    public function testCreatesUser()
    {
        $this->makeAuthenticatedApiRequest(self::METHOD_POST, self::USER_CREATE_NO_CONFIG_URI, ['cid' => 800001])->assertStatus(201);
        $this->assertDatabaseHas(
            'user',
            [
                'id' => 800001,
                'status' => UserStatus::ACTIVE
            ]
        );
    }

    public function testItRequiresUserAdminScopeWithConfig()
    {
        $this->regenerateAccessToken([], static::$tokenUser);
        $this->makeAuthenticatedApiRequest(self::METHOD_POST, self::USER_CREATE_URI)->assertStatus(403);
    }

    public function testCreateWithConfigUserReturnsTheCorrectJsonStructure()
    {
        $this->makeAuthenticatedApiRequest(self::METHOD_POST, self::USER_CREATE_URI)->assertJsonStructure(
            [
                'api-url',
                'api-key',
            ]
        );
    }

    public function testCreateUserWithConfigReturnsCreatedOnSuccess()
    {
        $this->makeAuthenticatedApiRequest(self::METHOD_POST, self::USER_CREATE_URI)->assertStatus(201);
    }

    public function testCreateUserWithConfigReturnsUnprocessableOnAlreadyExists()
    {
        $this->makeAuthenticatedApiRequest(self::METHOD_POST, self::USER_CREATE_URI)->assertStatus(201);
        $this->makeAuthenticatedApiRequest(self::METHOD_POST, self::USER_CREATE_URI)->assertStatus(422);
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

    public function testItGetsNotificationsForAUser()
    {
        $this->makeAuthenticatedApiRequest(self::METHOD_GET, 'user/1203533/notifications/unread')
            ->assertStatus(200)
            ->assertJsonStructure([
                [
                    "id",
                    "title",
                    "body",
                    "link",
                    "valid_from",
                    "valid_to",
                    "deleted_at",
                    "controllers"
                ]
            ]);
    }

    public function testItHandlesNoModelsFoundCorrectly()
    {
        // Unknown CID
        $this->makeAuthenticatedApiRequest(self::METHOD_GET, 'user/1500000/notifications/unread')
            ->assertStatus(404);

        // Unknown Notification Id
        $this->makeAuthenticatedApiRequest(self::METHOD_PUT, 'user/1203533/notifications/read/123456')
            ->assertStatus(404);

        // Unknown CID and Notification Id
        $this->makeAuthenticatedApiRequest(self::METHOD_PUT, 'user/1500000/notifications/read/123456')
            ->assertStatus(404);
    }

    public function testItHandlesReadingNotificationsCorrectly()
    {
        DB::table('notifications')->delete();

        $read = Notification::create([
            'title' => 'My Read Notification',
            'body' => 'This is some contents for my notification.',
            'link' => 'https://www.vatsim.uk',
            'valid_from' => Carbon::now()->subMonth(),
            'valid_to' => Carbon::now()->addYear()
        ]);

        $this->makeAuthenticatedApiRequest(self::METHOD_GET, 'user/1203533/notifications/unread')
            ->assertStatus(200)
            ->assertJsonCount(1);

        $this->makeAuthenticatedApiRequest(self::METHOD_PUT, "user/1203533/notifications/read/{$read->id}")
            ->assertStatus(200)
            ->assertJson(['message' => 'ok']);

        $this->makeAuthenticatedApiRequest(self::METHOD_GET, 'user/1203533/notifications/unread')
            ->assertStatus(200)
            ->assertJsonCount(0)
            ->assertJson([]);
    }

    public function testItReturnsInactiveUnreadNotificationsIfRequested()
    {
        DB::table('notifications')->delete();

        $read = Notification::create([
            'title' => 'My Read Notification',
            'body' => 'This is some contents for my notification.',
            'link' => 'https://www.vatsim.uk',
            'valid_from' => Carbon::now()->subMonth(),
            'valid_to' => Carbon::now()->addYear()
        ]);

        $this->makeAuthenticatedApiRequest(self::METHOD_GET, 'user/1203533/notifications/unread')
            ->assertStatus(200)
            ->assertJsonCount(1);

        $this->makeAuthenticatedApiRequest(self::METHOD_PUT, "user/1203533/notifications/read/{$read->id}")
            ->assertStatus(200)
            ->assertJson(['message' => 'ok']);

        $this->makeAuthenticatedApiRequest(self::METHOD_GET, 'user/1203533/notifications/unread')
            ->assertStatus(200)
            ->assertJsonCount(0)
            ->assertJson([]);

        $this->makeAuthenticatedApiRequest(self::METHOD_GET, 'user/1203533/notifications/unread', ['inactive' => true])
            ->assertStatus(200)
            ->assertJsonCount(0)
            ->assertJson([]);
    }
}
