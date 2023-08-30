<?php

namespace App\Listeners\Aircraft;

use App\Events\Aircraft\AircraftDataUpdatedEvent;
use App\Services\AircraftService;

class NotifyAircraftServiceOfDataUpdate {

    public function __construct(
        private readonly AircraftService $aircraftService
    ) {

    }

    public function handle(AircraftDataUpdatedEvent $event)
    {
        $this->aircraftService->aircraftDataUpdated();
    }
}
