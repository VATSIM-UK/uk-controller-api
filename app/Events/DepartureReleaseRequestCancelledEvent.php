<?php

namespace App\Events;

use App\Models\Release\Departure\DepartureReleaseRequest;
use Illuminate\Broadcasting\PrivateChannel;

class DepartureReleaseRequestCancelledEvent extends HighPriorityBroadcastEvent
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
        ];
    }

    public function broadcastOn()
    {
        return [new PrivateChannel('departure-releases')];
    }

    public function broadcastAs()
    {
        return 'departure_release.request_cancelled';
    }
}
