<?php

namespace App\Jobs\Squawk;

use App\Services\SquawkService;
use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;

class ReserveActiveSquawks
{
    use Dispatchable, Queueable;

    public function handle(SquawkService $squawkService): void
    {
        $squawkService->reserveSquawksInFirProximity();
    }
}
