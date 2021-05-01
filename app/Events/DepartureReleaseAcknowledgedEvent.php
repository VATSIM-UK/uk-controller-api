<?php

namespace App\Events;

use App\Models\Release\Departure\ControllerDepartureReleaseDecision;
use Illuminate\Broadcasting\PrivateChannel;

class DepartureReleaseAcknowledgedEvent extends HighPriorityBroadcastEvent
{
    private ControllerDepartureReleaseDecision $acknowledgement;

    public function __construct(ControllerDepartureReleaseDecision $acknowledgement)
    {
        $this->acknowledgement = $acknowledgement;
    }

    public function broadcastWith()
    {
        return [
            'id' => $this->acknowledgement->departure_release_request_id,
            'controller_position_id' => $this->acknowledgement->controller_position_id,
        ];
    }

    public function broadcastOn()
    {
        return [new PrivateChannel('departure-releases')];
    }

    public function broadcastAs()
    {
        return 'departure_release.acknowledged';
    }
}
