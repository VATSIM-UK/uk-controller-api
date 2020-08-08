<?php

namespace App\Events;

use App\Models\Release\Enroute\EnrouteRelease;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class EnrouteReleaseEvent implements ShouldBroadcast
{
    const CHANNEL = 'enroute-releases';

    /**
     * @var EnrouteRelease
     */
    private $release;

    public function __construct(EnrouteRelease $release)
    {
        $this->release = $release;
    }

    public function broadcastWith()
    {
        return [
            'callsign' => $this->release->callsign,
            'type' => $this->release->enroute_release_type_id,
            'release_point' => $this->release->release_point,
            'initiating_controller' => $this->release->initiating_controller,
            'target_controller' => $this->release->target_controller
        ];
    }

    public function broadcastOn()
    {
        return self::CHANNEL;
    }
}
