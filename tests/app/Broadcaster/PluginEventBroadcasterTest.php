<?php

namespace App\Broadcaster;

use App\BaseFunctionalTestCase;
use App\Models\Plugin\PluginEvent;
use Illuminate\Broadcasting\Channel;

class PluginEventBroadcasterTest extends BaseFunctionalTestCase
{
    public function testItBroadcastsEvents()
    {
        $broadcaster = new PluginEventBroadcaster();

        $event = 'someevent';
        $channel = new Channel('somechannel');
        $payload = [
            'socket' => null,
            'foo' => 'bar',
        ];
        $broadcaster->broadcast([$channel], $event, $payload);

        $this->assertDatabaseCount('plugin_events', 1);
        $latestEvent = PluginEvent::all()->first();
        $this->assertEquals(
            [
                'channel' => (string) $channel,
                'event' => $event,
                'data' => [
                    'foo' => 'bar',
                ],
            ],
            $latestEvent->event
        );
    }
}
