<?php

namespace App\Services\Acars;

use App\Helpers\Acars\TelexMessageInterface;

interface AcarsProviderInterface
{
    public function SendTelex(TelexMessageInterface $message): void;

    public function GetOnlineCallsigns(): array;
}
