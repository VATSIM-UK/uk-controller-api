<?php

namespace App\Jobs\Network;

use App\Services\NetworkControllerService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class IdentifyActiveControllerPositions implements ShouldQueue
{
    use Dispatchable, Queueable;

    public function handle(NetworkControllerService $controllerService): void
    {
        $controllerService->updatedMatchedControllerPositions();
    }
}
