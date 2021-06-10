<?php

namespace App\Events;

use App\Models\Release\Departure\DepartureReleaseRequest;
use Illuminate\Broadcasting\PrivateChannel;

class DepartureReleaseApprovedEvent extends HighPriorityBroadcastEvent
{
    private DepartureReleaseRequest $approval;

    public function __construct(DepartureReleaseRequest $approval)
    {
        $this->approval = $approval;
    }

    public function broadcastWith()
    {
        return [
            'id' => $this->approval->id,
            'expires_at' => $this->approval->release_expires_at
                ? $this->approval->release_expires_at->toDateTimeString()
                : null,
            'released_at' => $this->approval->release_valid_from,
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
