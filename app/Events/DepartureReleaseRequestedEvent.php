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
            'callsign' => $this->release->callsign,
            'requesting_controller' => $this->release->controller_position_id,
            'target_controllers' => $this->release->controllerPositions->pluck('id')->toArray(),
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
