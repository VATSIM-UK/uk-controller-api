<?php

namespace App\Helpers\Http;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;

trait MakesHttpRequests
{
    protected function httpRequest(): PendingRequest
    {
        return Http::withUserAgent('UKCP API')
            ->timeout(10);
    }
}
