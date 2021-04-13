<?php

namespace App\Broadcaster;

use App\Models\Plugin\PluginEvent;
use Illuminate\Broadcasting\Broadcasters\Broadcaster;
use Illuminate\Broadcasting\Broadcasters\PusherBroadcaster;
use Pusher\Pusher;

class PluginEventBroadcaster extends Broadcaster
{
    private PusherBroadcaster $pusherBroadcaster;

    public function __construct(PusherBroadcaster $pusherBroadcaster)
    {
        $this->pusherBroadcaster = $pusherBroadcaster;
    }

    /**
     * @codeCoverageIgnore
     */
    public function auth($request)
    {
        return $this->pusherBroadcaster->auth($request);
    }

    /**
     * @codeCoverageIgnore
     */
    public function validAuthenticationResponse($request, $result)
    {
        return $this->validAuthenticationResponse($request, $request);
    }

    public function broadcast(array $channels, $event, array $payload = [])
    {
        $this->pusherBroadcaster->broadcast($channels, $event, $payload);
        unset($payload['socket']);
        PluginEvent::create(
            [
                'event' => [
                    'channel' => (string) $channels[0],
                    'event' => $event,
                    'data' => $payload,
                ]
            ]
        );
    }
}
