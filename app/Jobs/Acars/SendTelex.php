<?php

namespace App\Jobs\Acars;

use App\Acars\Message\Telex\TelexMessageInterface;
use App\Acars\Provider\HoppieAcarsProvider;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\Middleware\RateLimited;
use Illuminate\Queue\SerializesModels;

class SendTelex implements ShouldQueue
{
    use Queueable, Dispatchable, SerializesModels;

    private TelexMessageInterface $telex;

    public function __construct(TelexMessageInterface $telex)
    {
        $this->telex = $telex;
    }

    public function handle(HoppieAcarsProvider $hoppie): void
    {
        $hoppie->sendTelexMessage($this->telex);
    }

    public function middleware(): array
    {
        return [new RateLimited('hoppie')];
    }
}
