<?php

namespace App\Events;

use App\Models\Release\Departure\DepartureReleaseRequest;
use Illuminate\Broadcasting\PrivateChannel;

class DepartureReleaseRequestedEvent extends HighPriorityBroadcastEvent
{
    private DepartureReleaseRequest $release;

    public function __construct(DepartureReleaseRequest $release)
    {
        $this->release = $release;
    }

    public function broadcastWith()
    {
        return [
            'id' => $this->release->id,
            'callsign' => $this->release->callsign,
            'requesting_controller' => $this->release->controller_position_id,
            'target_controller' => $this->release->target_controller_position_id,
            'expires_at' => $this->release->expires_at->toDateTimeString(),
        ];
    }

    public function broadcastOn()
    {
        return [new PrivateChannel('departure-releases')];
    }

    public function broadcastAs()
    {
        return 'departure_release.requested';
    }
}
