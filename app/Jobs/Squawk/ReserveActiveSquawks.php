<?php

namespace App\Jobs\Squawk;

use App\Services\SquawkService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class ReserveActiveSquawks implements ShouldQueue
{
    use Dispatchable, Queueable;

    public function handle(SquawkService $squawkService): void
    {
        $squawkService->reserveActiveSquawks();
    }
}
