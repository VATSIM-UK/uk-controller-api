<?php

namespace App\Listeners\Metar;

use App\Events\MetarsUpdatedEvent;
use App\Jobs\AltimeterSettingRegion\UpdateRegionalPressureSettings;
use App\Jobs\MinStack\UpdateMinimumStackLevels;
use Illuminate\Contracts\Queue\ShouldQueue;

class MetarsUpdated implements ShouldQueue
{
    public function handle(MetarsUpdatedEvent $event)
    {
        UpdateMinimumStackLevels::dispatch($event->getMetars());
        UpdateRegionalPressureSettings::dispatch($event->getMetars());
        return true;
    }
}
