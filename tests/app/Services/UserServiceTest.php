<?php


namespace App\Services;

use App\BaseFunctionalTestCase;
use App\Exceptions\UserAlreadyExistsException;
use App\Models\Notification\Notification;
use App\Models\User\User;
use App\Models\User\UserStatus;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
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
        $this->makeTestRequest('/authorise', $accessToken);
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

    public function testItGetsUnreadNotificationsForUser()
    {
        DB::table('notifications')->delete();
        DB::table('user')->delete();

        // Create a fake user
        $user = User::factory()->create();

        // Create 3 fake notifications
        $notification1 = Notification::factory()->create(['valid_from' => Carbon::now()->subHours(3)]);
        $notification2 = Notification::factory()->create(['valid_from' => Carbon::now()->subHours(2)]);
        $notification3 = Notification::factory()->create(['valid_from' => Carbon::now()->subHour()]);

        // Check that we get all 3 notifications
        $this->assertEquals([$notification3->id, $notification2->id, $notification1->id], $this->service->getUnreadNotificationsForUser($user->id)->pluck('id')->toArray());

        // Mark one of the notifications as having already finished
        $notification2->update(['valid_to' => Carbon::now()->subMinutes(10)]);

        // Check that we only get 2 notifications
        $this->assertEquals([$notification3->id, $notification1->id], $this->service->getUnreadNotificationsForUser($user->id)->pluck('id')->toArray());

        // Check that we still get 3 notifications if we include inactive ones
        $this->assertEquals([$notification3->id, $notification2->id, $notification1->id], $this->service->getUnreadNotificationsForUser($user->id, true)->pluck('id')->toArray());

        // Mark a notification as read by the user
        $notification1->readBy()->attach($user->id);

        // Check that we only get 2 notifications
        $this->assertEquals([$notification3->id, $notification2->id], $this->service->getUnreadNotificationsForUser($user->id, true)->pluck('id')->toArray());

        // Mark another notification as soft deleted
        $notification3->delete();

        // Check that we only get 1 notification
        $this->assertEquals([$notification2->id], $this->service->getUnreadNotificationsForUser($user->id, true)->pluck('id')->toArray());
    }

    public function testItGetsUnreadNotificationsForUserAndCreatesTheUser()
    {
        DB::table('notifications')->delete();
        DB::table('user')->delete();

        // Create a fake user
        $userId = 123456;

        // Create 3 fake notifications
        $notification1 = Notification::factory()->create(['valid_from' => Carbon::now()->subHours(3)]);
        $notification2 = Notification::factory()->create(['valid_from' => Carbon::now()->subHours(2)]);
        $notification3 = Notification::factory()->create(['valid_from' => Carbon::now()->subHour()]);

        // Check that we get all 3 notifications
        $this->assertEquals([$notification3->id, $notification2->id, $notification1->id], $this->service->getUnreadNotificationsForUser($userId)->pluck('id')->toArray());

        // Mark one of the notifications as having already finished
        $notification2->update(['valid_to' => Carbon::now()->subMinutes(10)]);

        // Check that we only get 2 notifications
        $this->assertEquals([$notification3->id, $notification1->id], $this->service->getUnreadNotificationsForUser($userId)->pluck('id')->toArray());

        // Check that we still get 3 notifications if we include inactive ones
        $this->assertEquals([$notification3->id, $notification2->id, $notification1->id], $this->service->getUnreadNotificationsForUser($userId, true)->pluck('id')->toArray());

        // Mark a notification as read by the user
        $notification1->readBy()->attach($userId);

        // Check that we only get 2 notifications
        $this->assertEquals([$notification3->id, $notification2->id], $this->service->getUnreadNotificationsForUser($userId, true)->pluck('id')->toArray());

        // Mark another notification as soft deleted
        $notification3->delete();

        // Check that we only get 1 notification
        $this->assertEquals([$notification2->id], $this->service->getUnreadNotificationsForUser($userId, true)->pluck('id')->toArray());

        // Check that the user was created
        $this->assertDatabaseHas('user', ['id' => $userId, 'status' => UserStatus::ACTIVE]);
    }

    public function testItMarksANotificationAsReadForAUser()
    {
        DB::table('notifications')->delete();
        DB::table('user')->delete();

        // Create a fake user
        $user = User::factory()->create();

        // Create a fake notification
        $notification = Notification::factory()->create();

        // Mark the notification as read
        $this->service->markNotificationAsReadForUser($user->id, $notification->id);

        // Check that the notification is marked as read
        $this->assertDatabaseHas('notification_user', ['notification_id' => $notification->id, 'user_id' => $user->id]);
    }

    public function testItDoesntMarkANotificationAsReadForAUserIfItDoesntExist()
    {
        DB::table('notifications')->delete();
        DB::table('user')->delete();

        // Create a fake notification
        $notification = Notification::factory()->create();

        $this->expectException(ModelNotFoundException::class);
        $this->service->markNotificationAsReadForUser(123, $notification->id);

        // Check that the notification is not marked as read
        $this->assertDatabaseMissing('notification_user', ['notification_id' => $notification->id]);
    }

    public function testItDoesntMarkANotificationAsReadForAUserIfTheNotificationDoesntExist()
    {
        DB::table('notifications')->delete();
        DB::table('user')->delete();

        // Create a fake user
        $user = User::factory()->create();

        $this->expectException(ModelNotFoundException::class);
        $this->service->markNotificationAsReadForUser($user->id, 123);

        // Check that the notification is not marked as read
        $this->assertDatabaseMissing('notification_user', ['user_id' => $user->id]);
    }

    private function makeTestRequest(string $uri, string $token)
    {
        $this->get('api' . $uri, ['Authorization' => 'Bearer ' . $token])->assertStatus(200);
    }
}
