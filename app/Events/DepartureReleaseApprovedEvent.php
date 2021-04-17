<?php

namespace App\Events;

use App\Models\Release\Departure\ControllerDepartureReleaseDecision;
use Illuminate\Broadcasting\PrivateChannel;

class DepartureReleaseApprovedEvent extends HighPriorityBroadcastEvent
{
    private ControllerDepartureReleaseDecision $approval;

    public function __construct(ControllerDepartureReleaseDecision $approval)
    {
        $this->approval = $approval;
    }

    public function broadcastWith()
    {
        return [
            'id' => $this->approval->departure_release_request_id,
            'controller_position_id' => $this->approval->controller_position_id,
            'expires_at' => $this->approval->release_expires_at->toDateTimeString(),
        ];
    }

    public function broadcastOn()
    {
        return [new PrivateChannel('departure-releases')];
    }

    public function broadcastAs()
    {
        return 'departure_release.approved';
    }
}
