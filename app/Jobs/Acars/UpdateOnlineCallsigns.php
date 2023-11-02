<?php

namespace App\Jobs\Acars;

use App\Acars\Provider\HoppieAcarsProvider;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\Middleware\RateLimited;
use Illuminate\Queue\SerializesModels;

class UpdateOnlineCallsigns implements ShouldQueue
{
    use Queueable, Dispatchable, SerializesModels;

    public function handle(HoppieAcarsProvider $hoppie): void
    {
        $hoppie->setOnlineCallsigns();
    }

    public function middleware(): array
    {
        return [new RateLimited('hoppie')];
    }
}
