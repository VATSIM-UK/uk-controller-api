<?php

namespace App\Broadcaster;

use App\Models\Plugin\PluginEvent;
use Illuminate\Broadcasting\Broadcasters\Broadcaster;

class PluginEventBroadcaster extends Broadcaster
{
    /**
     * @codeCoverageIgnore
     */
    public function auth($request)
    {
        return true;
    }

    /**
     * @codeCoverageIgnore
     */
    public function validAuthenticationResponse($request, $result)
    {
        return true;
    }

    public function broadcast(array $channels, $event, array $payload = [])
    {
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
