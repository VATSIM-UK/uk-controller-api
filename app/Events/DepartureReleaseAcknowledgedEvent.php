<?php

namespace App\Events;

use App\Models\Release\Departure\DepartureReleaseRequest;
use Illuminate\Broadcasting\PrivateChannel;

class DepartureReleaseAcknowledgedEvent extends HighPriorityBroadcastEvent
{
    private DepartureReleaseRequest $acknowledgement;

    public function __construct(DepartureReleaseRequest $acknowledgement)
    {
        $this->acknowledgement = $acknowledgement;
    }

    public function broadcastWith()
    {
        return [
            'id' => $this->acknowledgement->id,
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
