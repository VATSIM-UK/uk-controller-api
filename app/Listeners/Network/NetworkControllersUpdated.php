<?php

namespace App\Listeners\Network;

use App\Jobs\Network\IdentifyActiveControllerPositions;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Bus;

class NetworkControllersUpdated implements ShouldQueue, ShouldBeUnique
{
    // This listener should be unique for 30 seconds
    public $uniqueFor = 30;

    public function handle()
    {
        Bus::chain(
            [
                new IdentifyActiveControllerPositions()
            ]
        )->dispatch();
        return true;
    }
}
