<?php

namespace App\Events;

use App\Models\Release\Departure\DepartureReleaseRequest;
use Illuminate\Broadcasting\PrivateChannel;

class DepartureReleaseRejectedEvent extends HighPriorityBroadcastEvent
{
    private DepartureReleaseRequest $rejection;

    public function __construct(DepartureReleaseRequest $rejection)
    {
        $this->rejection = $rejection;
    }

    public function broadcastWith()
    {
        return [
            'id' => $this->rejection->id,
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
