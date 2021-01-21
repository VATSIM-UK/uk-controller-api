<?php

namespace App\Http\Controllers;

use App\BaseApiTestCase;
use App\Models\Controller\ControllerPosition;
use App\Models\Notification\Notification;
use Carbon\Carbon;

class NotificationControllerTest extends BaseApiTestCase
{
    public function testItDoesNotAllowUnauthenticatedRequests()
    {
        $this->makeUnauthenticatedApiRequest(self::METHOD_GET, 'notifications')
            ->assertStatus(401);
    }

    public function testItShowsNotificationsWithinTheirValidPeriod()
    {
        // Current Notification
        Notification::create([
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
        Notification::create([
            'title' => 'My Active Notification',
            'body' => 'This is some contents for my notification.',
            'valid_from' => Carbon::now()->subYear(),
            'valid_to' => Carbon::now()->addYear()
        ]);

        // Inactive Notification
        Notification::create([
            'title' => 'My Inactive Notification',
            'body' => 'This is some contents for my notification.',
            'valid_from' => Carbon::now()->subYear(),
            'valid_to' => Carbon::now()->addYear(),
            'disabled_at' => Carbon::now()
        ]);

        $expected = [
            [
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
        Notification::create([
            'title' => 'My Second Notification',
            'body' => 'This is some contents for my notification.',
            'valid_from' => Carbon::now()->subMonth(),
            'valid_to' => Carbon::now()->addYear()
        ]);

        Notification::create([
            'title' => 'My Third Notification',
            'body' => 'This is some contents for my notification.',
            'valid_from' => Carbon::now()->subYear(),
            'valid_to' => Carbon::now()->addYear()
        ]);

        Notification::create([
            'title' => 'My First Notification',
            'body' => 'This is some contents for my notification.',
            'valid_from' => Carbon::now()->subWeek(),
            'valid_to' => Carbon::now()->addYear()
        ]);

        $expected = [
            [
                'title' => 'My First Notification',
                'body' => 'This is some contents for my notification.',
                'valid_from' => Carbon::now()->subWeek()->toDateTimeString(),
                'valid_to' => Carbon::now()->addYear()->toDateTimeString(),
                'controllers' => []
            ],
            [
                'title' => 'My Second Notification',
                'body' => 'This is some contents for my notification.',
                'valid_from' => Carbon::now()->subMonth()->toDateTimeString(),
                'valid_to' => Carbon::now()->addYear()->toDateTimeString(),
                'controllers' => []
            ],
            [
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
}
