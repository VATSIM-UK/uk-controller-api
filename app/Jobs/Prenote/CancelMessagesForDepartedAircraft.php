<?php

namespace App\Jobs\Prenote;

use App\Services\PrenoteMessageService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class CancelMessagesForDepartedAircraft implements ShouldQueue
{
    use Dispatchable, Queueable;

    public function handle(PrenoteMessageService $prenoteMessageService): void
    {
        $prenoteMessageService->cancelMessagesForAirborneAircraft();
    }
}
