<?php

namespace App\Events;

use App\Models\Release\Departure\ControllerDepartureReleaseDecision;
use Illuminate\Broadcasting\PrivateChannel;

class DepartureReleaseRejectedEvent extends HighPriorityBroadcastEvent
{
    private ControllerDepartureReleaseDecision $rejection;

    public function __construct(ControllerDepartureReleaseDecision $rejection)
    {
        $this->rejection = $rejection;
    }

    public function broadcastWith()
    {
        return [
            'id' => $this->rejection->departure_release_request_id,
            'controller_position_id' => $this->rejection->controller_position_id,
        ];
    }

    public function broadcastOn()
    {
        return [new PrivateChannel('departure-releases')];
    }

    public function broadcastAs()
    {
        return 'departure_release.rejected';
    }
}
