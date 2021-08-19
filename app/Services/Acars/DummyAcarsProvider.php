<?php

namespace App\Services\Acars;

use App\Helpers\Acars\TelexMessageInterface;
use Illuminate\Support\Collection;

class DummyAcarsProvider implements AcarsProviderInterface
{
    public function SendTelex(TelexMessageInterface $message): void
    {
        // Nothing to do - is dummy
    }

    public function GetOnlineCallsigns(): Collection
    {
        return collect();
    }
}
