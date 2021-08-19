<?php

namespace App\Services\Acars;

use App\Helpers\Acars\TelexMessageInterface;

class DummyAcarsProvider implements AcarsProviderInterface
{
    public function SendTelex(TelexMessageInterface $message): void
    {
    }

    public function GetOnlineCallsigns(): array
    {
        return [];
    }
}
