<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\BaseApiTestCase;
use Illuminate\Support\Facades\DB;
use App\Models\Notification\Notification;
use App\Models\Controller\ControllerPosition;

class NotificationControllerTest extends BaseApiTestCase
{
    public function setUp(): void
    {
        parent::setUp();
        Carbon::setTestNow(Carbon::now());
    }

    public function testItDoesNotAllowUnauthenticatedRequests()
    {
        $this->makeUnauthenticatedApiRequest(self::METHOD_GET, 'notifications')
            ->assertStatus(401);
    }

    public function testItShowsNotificationsWithinTheirValidPeriod()
    {
        // Current Notification
        $current = Notification::create([
            'title' => 'My Current Notification',
            'body' => 'This is some contents for my notification.',
            'link' => 'https://www.vatsim.uk',
            'valid_from' => Carbon::now()->subYear(),
            'valid_to' => Carbon::now()->addYear()
        ]);

        // Old Notification
        Notification::create([
            'title' => 'My Old Notification',
            'body' => 'This is some contents for my notification.',
            'link' => 'https://www.vatsim.uk',
            'valid_from' => Carbon::now()->subYear(),
            'valid_to' => Carbon::now()->subDay()
        ]);

        $expected = [
            [
                'id' => $current->id,
                'title' => 'My Current Notification',
                'body' => 'This is some contents for my notification.',
                'link' => 'https://www.vatsim.uk',
                'valid_from' => Carbon::now()->subYear()->toDateTimeString(),
                'valid_to' => Carbon::now()->addYear()->toDateTimeString(),
                'controllers' => []
            ]
        ];

        $this->makeAuthenticatedApiRequest(self::METHOD_GET, 'notifications')
            ->assertStatus(200)
            ->assertJson($expected);
    }

    public function testItDoesNotShowDisabledNotifications()
    {
        // Active Notification
        $active = Notification::create([
            'title' => 'My Active Notification',
            'body' => 'This is some contents for my notification.',
            'link' => 'https://www.vatsim.uk',
            'valid_from' => Carbon::now()->subYear(),
            'valid_to' => Carbon::now()->addYear()
        ]);

        // Inactive Notification
        $inactive = Notification::create([
            'title' => 'My Inactive Notification',
            'body' => 'This is some contents for my notification.',
            'link' => 'https://www.vatsim.uk',
            'valid_from' => Carbon::now()->subYear(),
            'valid_to' => Carbon::now()->addYear()
        ]);
        $inactive->delete();

        $expected = [
            [
                'id' => $active->id,
                'title' => 'My Active Notification',
                'body' => 'This is some contents for my notification.',
                'link' => 'https://www.vatsim.uk',
                'valid_from' => Carbon::now()->subYear()->toDateTimeString(),
                'valid_to' => Carbon::now()->addYear()->toDateTimeString(),
                'controllers' => []
            ]
        ];

        $this->makeAuthenticatedApiRequest(self::METHOD_GET, 'notifications')
            ->assertStatus(200)
            ->assertJson($expected);
    }

    public function testItShowsNotificationsInOrderOfValidFromDate()
    {
        $second = Notification::create([
            'title' => 'My Second Notification',
            'body' => 'This is some contents for my notification.',
            'link' => 'https://www.vatsim.uk',
            'valid_from' => Carbon::now()->subMonth(),
            'valid_to' => Carbon::now()->addYear()
        ]);

        $third = Notification::create([
            'title' => 'My Third Notification',
            'body' => 'This is some contents for my notification.',
            'link' => 'https://www.vatsim.uk',
            'valid_from' => Carbon::now()->subYear(),
            'valid_to' => Carbon::now()->addYear()
        ]);

        $first = Notification::create([
            'title' => 'My First Notification',
            'body' => 'This is some contents for my notification.',
            'link' => 'https://www.vatsim.uk',
            'valid_from' => Carbon::now()->subWeek(),
            'valid_to' => Carbon::now()->addYear()
        ]);

        $expected = [
            [
                'id' => $first->id,
                'title' => 'My First Notification',
                'body' => 'This is some contents for my notification.',
                'link' => 'https://www.vatsim.uk',
                'valid_from' => Carbon::now()->subWeek()->toDateTimeString(),
                'valid_to' => Carbon::now()->addYear()->toDateTimeString(),
                'controllers' => []
            ],
            [
                'id' => $second->id,
                'title' => 'My Second Notification',
                'body' => 'This is some contents for my notification.',
                'link' => 'https://www.vatsim.uk',
                'valid_from' => Carbon::now()->subMonth()->toDateTimeString(),
                'valid_to' => Carbon::now()->addYear()->toDateTimeString(),
                'controllers' => []
            ],
            [
                'id' => $third->id,
                'title' => 'My Third Notification',
                'body' => 'This is some contents for my notification.',
                'link' => 'https://www.vatsim.uk',
                'valid_from' => Carbon::now()->subYear()->toDateTimeString(),
                'valid_to' => Carbon::now()->addYear()->toDateTimeString(),
                'controllers' => []
            ]
        ];

        $this->makeAuthenticatedApiRequest(self::METHOD_GET, 'notifications')
            ->assertStatus(200)
            ->assertJson($expected);
    }

    public function testANotificationCanBeLinkedToAControllerPosition()
    {
        $notification = Notification::create([
            'title' => 'My Linked Notification',
            'body' => 'This is some contents for my notification.',
            'link' => 'https://www.vatsim.uk',
            'valid_from' => Carbon::now()->subMonth(),
            'valid_to' => Carbon::now()->addYear()
        ]);

        $notification->controllers()->attach(ControllerPosition::first());

        $expected = [
            [
                'id' => $notification->id,
                'title' => 'My Linked Notification',
                'body' => 'This is some contents for my notification.',
                'link' => 'https://www.vatsim.uk',
                'valid_from' => Carbon::now()->subMonth()->toDateTimeString(),
                'valid_to' => Carbon::now()->addYear()->toDateTimeString(),
                'controllers' => [
                    'EGLL_S_TWR',
                ]
            ]
        ];

        $this->makeAuthenticatedApiRequest(self::METHOD_GET, 'notifications')
            ->assertStatus(200)
            ->assertJson($expected);
    }

    public function testANotificationCanBeRead()
    {
        $notification = Notification::create([
            'title' => 'My Linked Notification',
            'body' => 'This is some contents for my notification.',
            'link' => 'https://www.vatsim.uk',
            'valid_from' => Carbon::now()->subMonth(),
            'valid_to' => Carbon::now()->addYear()
        ]);

        $this->assertCount(0, $notification->readBy);
        $this->assertDatabaseCount('notification_user', 0);

        $this->makeAuthenticatedApiRequest(self::METHOD_PUT, "notifications/read/{$notification->id}")
            ->assertStatus(200)
            ->assertJson(['message' => 'ok']);

        $this->assertCount(1, $notification->fresh()->readBy);
        $this->assertDataBaseHas('notification_user', [
            'user_id' => auth()->user()->id,
            'notification_id' => $notification->id
        ]);
    }

    public function testItCannotReadANonNumericNotification()
    {
        $this->makeAuthenticatedApiRequest(self::METHOD_PUT, "notifications/read/my-string")
            ->assertStatus(404);
    }

    public function testItCanReturnUnreadNotifications()
    {
        $read = Notification::create([
            'title' => 'My Read Notification',
            'body' => 'This is some contents for my notification.',
            'link' => 'https://www.vatsim.uk',
            'valid_from' => Carbon::now()->subMonth(),
            'valid_to' => Carbon::now()->addYear()
        ]);

        $unread = Notification::create([
            'title' => 'My Unread Notification',
            'body' => 'This is some contents for my notification.',
            'link' => 'https://www.vatsim.uk',
            'valid_from' => Carbon::now()->subMonth(),
            'valid_to' => Carbon::now()->addYear()
        ]);

        $this->makeAuthenticatedApiRequest(self::METHOD_GET, 'notifications/unread')
            ->assertStatus(200)
            ->assertJsonCount(2);

        $this->makeAuthenticatedApiRequest(self::METHOD_PUT, "notifications/read/{$read->id}")
            ->assertStatus(200)
            ->assertJson(['message' => 'ok']);

        $expected = [
            [
                'id' => $unread->id,
                'title' => 'My Unread Notification',
                'body' => 'This is some contents for my notification.',
                'link' => 'https://www.vatsim.uk',
                'valid_from' => Carbon::now()->subMonth()->toDateTimeString(),
                'valid_to' => Carbon::now()->addYear()->toDateTimeString(),
                'controllers' => []
            ]
        ];

        $this->makeAuthenticatedApiRequest(self::METHOD_GET, 'notifications/unread')
            ->assertStatus(200)
            ->assertJsonCount(1)
            ->assertJson($expected);
    }

    public function testItOnlyReturnsUnreadNotificationsForTheUserLoggedIn()
    {
        // Create the two active notifications
        $read = Notification::create([
            'title' => 'My Read Notification',
            'body' => 'This is some contents for my notification.',
            'link' => 'https://www.vatsim.uk',
            'valid_from' => Carbon::now()->subMonth(),
            'valid_to' => Carbon::now()->addYear()
        ]);

        $unread = Notification::create([
            'title' => 'My Unread Notification',
            'body' => 'This is some contents for my notification.',
            'link' => 'https://www.vatsim.uk',
            'valid_from' => Carbon::now()->subMonth(),
            'valid_to' => Carbon::now()->addYear()
        ]);

        // Create a record of another user reading this notification
        DB::table('notification_user')->insert([
            'notification_id' => $read->id,
            'user_id' => 1203535,
            'created_at' => '2020-01-01',
            'updated_at' => '2020-01-01'
        ]);

        // Get the logged in user's unread notifications
        $this->makeAuthenticatedApiRequest(self::METHOD_GET, 'notifications/unread')
            ->assertStatus(200)
            ->assertJsonCount(2);

        // Read a notification
        $this->makeAuthenticatedApiRequest(self::METHOD_PUT, "notifications/read/{$read->id}")
            ->assertStatus(200)
            ->assertJson(['message' => 'ok']);

        $expected = [
            [
                'id' => $unread->id,
                'title' => 'My Unread Notification',
                'body' => 'This is some contents for my notification.',
                'link' => 'https://www.vatsim.uk',
                'valid_from' => Carbon::now()->subMonth()->toDateTimeString(),
                'valid_to' => Carbon::now()->addYear()->toDateTimeString(),
                'controllers' => []
            ]
        ];

        // Assert only the unread notification is returned
        $this->makeAuthenticatedApiRequest(self::METHOD_GET, 'notifications/unread')
            ->assertStatus(200)
            ->assertJsonCount(1)
            ->assertJson($expected);
    }

    public function testItHandlesNoUnreadNotificationsCorrectly()
    {
        $read = Notification::create([
            'title' => 'My Read Notification',
            'body' => 'This is some contents for my notification.',
            'link' => 'https://www.vatsim.uk',
            'valid_from' => Carbon::now()->subMonth(),
            'valid_to' => Carbon::now()->addYear()
        ]);

        $this->makeAuthenticatedApiRequest(self::METHOD_PUT, "notifications/read/{$read->id}")
            ->assertStatus(200)
            ->assertJson(['message' => 'ok']);

        $this->makeAuthenticatedApiRequest(self::METHOD_GET, 'notifications/unread')
            ->assertStatus(200)
            ->assertJsonCount(0)
            ->assertJson([]);
    }
}
