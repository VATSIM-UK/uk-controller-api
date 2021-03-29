<?php

namespace App\Listeners\Network;

use App\Jobs\Stand\OccupyStands;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Bus;

class NetworkDataUpdated implements ShouldQueue, ShouldBeUnique
{
    // This listener should be unique for 5 minutes.
    public $uniqueFor = 300;

    public function handle()
    {
        Bus::chain(
            [
                new OccupyStands(),
            ]
        )->dispatch();
        return true;
    }
}
