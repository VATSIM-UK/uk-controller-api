<?php

namespace App\Listeners\Metar;

use App\Events\MetarsUpdatedEvent;
use App\Services\MinStackLevelService;
use Illuminate\Contracts\Queue\ShouldQueue;

class UpdateMinimumStackLevels implements ShouldQueue
{
    private MinStackLevelService $service;

    public function __construct(MinStackLevelService $service)
    {
        $this->service = $service;
    }

    public function handle(MetarsUpdatedEvent $metars): bool
    {
        $this->service->updateMinimumStackLevelsFromMetars($metars->getMetars());
        return true;
    }
}
