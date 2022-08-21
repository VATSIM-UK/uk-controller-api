<?php

namespace App\Models\Notification;

use App\BaseFunctionalTestCase;
use Carbon\Carbon;

class NotificationTest extends BaseFunctionalTestCase
{
    public function testItConvertsToArray()
    {
        Carbon::setTestNow(Carbon::now()->startOfSecond());
        $notification = Notification::create(
            [
                'title' => 'Some title',
                'body' => 'Some body',
                'link' => 'Some link',
                'valid_from' => Carbon::now(),
                'valid_to' => Carbon::now()->addHour(),
            ]
        );
        $notification->controllers()->sync([1, 2]);

        $expected = [
            'id' => $notification->id,
            'title' => 'Some title',
            'body' => 'Some body',
            'link' => 'Some link',
            'valid_from' => Carbon::now(),
            'valid_to' => Carbon::now()->addHour(),
            'controllers' => [
                'EGLL_S_TWR',
                'EGLL_N_APP',
            ],
        ];
        $this->assertEquals($expected, $notification->toArray());
    }
}
