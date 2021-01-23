<?php

namespace App\Http\Controllers;

use App\BaseApiTestCase;
use App\Models\Controller\ControllerPosition;
use App\Models\Notification\Notification;
use Carbon\Carbon;

class NotificationControllerTest extends BaseApiTestCase
{
    public function setUp(): void
    {
        parent::setUp();
        Carbon::setTestNow();
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
            'valid_from' => Carbon::now()->subYear(),
            'valid_to' => Carbon::now()->addYear()
        ]);

        // Old Notification
        Notification::create([
            'title' => 'My Old Notification',
            'body' => 'This is some contents for my notification.',
            'valid_from' => Carbon::now()->subYear(),
            'valid_to' => Carbon::now()->subDay()
        ]);

        $expected = [
            [
                'id' => $current->id,
                'title' => 'My Current Notification',
                'body' => 'This is some contents for my notification.',
                'valid_from' => Carbon::now()->subYear()->toDateTimeString(),
                'valid_to' => Carbon::now()->addYear()->toDateTimeString(),
                'controllers' => []
            ]
        ];

        $this->makeAuthenticatedApiRequest(self::METHOD_GET, 'notifications')
            ->assertStatus(200)
            ->assertExactJson($expected);
    }

    public function testItDoesNotShowDisabledNotifications()
    {
        // Active Notification
        $active = Notification::create([
            'title' => 'My Active Notification',
            'body' => 'This is some contents for my notification.',
            'valid_from' => Carbon::now()->subYear(),
            'valid_to' => Carbon::now()->addYear()
        ]);

        // Inactive Notification
        $inactive = Notification::create([
            'title' => 'My Inactive Notification',
            'body' => 'This is some contents for my notification.',
            'valid_from' => Carbon::now()->subYear(),
            'valid_to' => Carbon::now()->addYear()
        ]);
        $inactive->delete();

        $expected = [
            [
                'id' => $active->id,
                'title' => 'My Active Notification',
                'body' => 'This is some contents for my notification.',
                'valid_from' => Carbon::now()->subYear()->toDateTimeString(),
                'valid_to' => Carbon::now()->addYear()->toDateTimeString(),
                'controllers' => []
            ]
        ];

        $this->makeAuthenticatedApiRequest(self::METHOD_GET, 'notifications')
            ->assertStatus(200)
            ->assertExactJson($expected);
    }

    public function testItShowsNotificationsInOrderOfValidFromDate()
    {
        $second = Notification::create([
            'title' => 'My Second Notification',
            'body' => 'This is some contents for my notification.',
            'valid_from' => Carbon::now()->subMonth(),
            'valid_to' => Carbon::now()->addYear()
        ]);

        $third = Notification::create([
            'title' => 'My Third Notification',
            'body' => 'This is some contents for my notification.',
            'valid_from' => Carbon::now()->subYear(),
            'valid_to' => Carbon::now()->addYear()
        ]);

        $first = Notification::create([
            'title' => 'My First Notification',
            'body' => 'This is some contents for my notification.',
            'valid_from' => Carbon::now()->subWeek(),
            'valid_to' => Carbon::now()->addYear()
        ]);

        $expected = [
            [
                'id' => $first->id,
                'title' => 'My First Notification',
                'body' => 'This is some contents for my notification.',
                'valid_from' => Carbon::now()->subWeek()->toDateTimeString(),
                'valid_to' => Carbon::now()->addYear()->toDateTimeString(),
                'controllers' => []
            ],
            [
                'id' => $second->id,
                'title' => 'My Second Notification',
                'body' => 'This is some contents for my notification.',
                'valid_from' => Carbon::now()->subMonth()->toDateTimeString(),
                'valid_to' => Carbon::now()->addYear()->toDateTimeString(),
                'controllers' => []
            ],
            [
                'id' => $third->id,
                'title' => 'My Third Notification',
                'body' => 'This is some contents for my notification.',
                'valid_from' => Carbon::now()->subYear()->toDateTimeString(),
                'valid_to' => Carbon::now()->addYear()->toDateTimeString(),
                'controllers' => []
            ]
        ];

        $this->makeAuthenticatedApiRequest(self::METHOD_GET, 'notifications')
            ->assertStatus(200)
            ->assertExactJson($expected);
    }

    public function testANotificationCanBeLinkedToAControllerPosition()
    {
        $notification = Notification::create([
            'title' => 'My Linked Notification',
            'body' => 'This is some contents for my notification.',
            'valid_from' => Carbon::now()->subMonth(),
            'valid_to' => Carbon::now()->addYear()
        ]);

        $notification->controllers()->attach(ControllerPosition::first());

        $expected = [
            [
                'id' => $notification->id,
                'title' => 'My Linked Notification',
                'body' => 'This is some contents for my notification.',
                'valid_from' => Carbon::now()->subMonth()->toDateTimeString(),
                'valid_to' => Carbon::now()->addYear()->toDateTimeString(),
                'controllers' => [
                    ['callsign' => 'EGLL_S_TWR']
                ]
            ]
        ];

        $this->makeAuthenticatedApiRequest(self::METHOD_GET, 'notifications')
            ->assertStatus(200)
            ->assertExactJson($expected);
    }

    public function testANotificationCanBeRead()
    {
        $notification = Notification::create([
            'title' => 'My Linked Notification',
            'body' => 'This is some contents for my notification.',
            'valid_from' => Carbon::now()->subMonth(),
            'valid_to' => Carbon::now()->addYear()
        ]);

        $this->assertCount(0, $notification->readBy);
        $this->assertDatabaseCount('notification_reads', 0);

        $this->makeAuthenticatedApiRequest(self::METHOD_PUT, "notifications/read/{$notification->id}")
            ->assertStatus(200)
            ->assertExactJson(['message' => 'ok']);

        $this->assertCount(1, $notification->fresh()->readBy);
        $this->assertDataBaseHas('notification_reads', [
            'user_id' => auth()->user()->id,
            'notification_id' => $notification->id
        ]);
    }

    public function testItCanReturnUnreadNotifications()
    {
        $read = Notification::create([
            'title' => 'My Read Notification',
            'body' => 'This is some contents for my notification.',
            'valid_from' => Carbon::now()->subMonth(),
            'valid_to' => Carbon::now()->addYear()
        ]);

        $unread = Notification::create([
            'title' => 'My Unread Notification',
            'body' => 'This is some contents for my notification.',
            'valid_from' => Carbon::now()->subMonth(),
            'valid_to' => Carbon::now()->addYear()
        ]);

        $this->makeAuthenticatedApiRequest(self::METHOD_GET, 'notifications/unread')
            ->assertStatus(200)
            ->assertJsonCount(2);

        $this->makeAuthenticatedApiRequest(self::METHOD_PUT, "notifications/read/{$read->id}")
            ->assertStatus(200)
            ->assertExactJson(['message' => 'ok']);

        $expected = [
            [
                'id' => $unread->id,
                'title' => 'My Unread Notification',
                'body' => 'This is some contents for my notification.',
                'valid_from' => Carbon::now()->subMonth()->toDateTimeString(),
                'valid_to' => Carbon::now()->addYear()->toDateTimeString(),
                'controllers' => []
            ]
        ];

        $this->makeAuthenticatedApiRequest(self::METHOD_GET, 'notifications/unread')
            ->assertStatus(200)
            ->assertJsonCount(1)
            ->assertExactJson($expected);
    }
}
