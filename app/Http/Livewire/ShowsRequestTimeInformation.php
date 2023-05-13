<?php

namespace App\Http\Livewire;

use Carbon\Carbon;

trait ShowsRequestTimeInformation
{
    private function getRequestTimeViewData(Carbon $requestedTime): array
    {
        return [
            'startTime' => $requestedTime->copy()->subMinutes(40),
            'endTime' => $requestedTime->copy()->addMinutes(20),
        ];
    }
}
