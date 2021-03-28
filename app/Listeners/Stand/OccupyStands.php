<?php

namespace App\Listeners\Stand;

use App\Events\NetworkDataUpdatedEvent;
use App\Services\StandService;
use Illuminate\Contracts\Queue\ShouldQueue;

class OccupyStands implements ShouldQueue
{
    private StandService $standService;

    public function __construct(StandService $standService)
    {
        $this->standService = $standService;
    }

    public function handle(NetworkDataUpdatedEvent $event) : bool
    {
        $this->standService->setOccupiedStands();
        return true;
    }
}
