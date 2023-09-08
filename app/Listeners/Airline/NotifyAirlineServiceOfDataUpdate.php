<?php

namespace App\Listeners\Airline;

use App\Events\Airline\AirlinesUpdatedEvent;
use App\Services\AirlineService;

class NotifyAirlineServiceOfDataUpdate
{
    public function __construct(
        private readonly AirlineService $airlineService
    ) {
    }

    public function handle(AirlinesUpdatedEvent $event)
    {
        $this->airlineService->airlinesUpdated();
    }
}
