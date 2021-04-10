<?php

namespace App\Broadcaster;

use App\BaseFunctionalTestCase;
use App\Models\Plugin\PluginEvent;
use Illuminate\Broadcasting\Broadcasters\PusherBroadcaster;
use Illuminate\Broadcasting\Channel;
use Mockery;

class PluginEventBroadcasterTest extends BaseFunctionalTestCase
{
    public function testItBroadcastsEvents()
    {
        $mockPusher = Mockery::mock(PusherBroadcaster::class);
        $broadcaster = new PluginEventBroadcaster($mockPusher);

        $event = 'someevent';
        $channel = new Channel('somechannel');
        $payload = [
            'socket' => null,
            'foo' => 'bar',
        ];

        $mockPusher->shouldReceive('broadcast')->with([$channel], $event, $payload)
            ->once();

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
