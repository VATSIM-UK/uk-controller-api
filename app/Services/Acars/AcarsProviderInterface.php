<?php

namespace App\Services\Acars;

use App\Helpers\Acars\TelexMessageInterface;
use Illuminate\Support\Collection;

interface AcarsProviderInterface
{
    public function SendTelex(TelexMessageInterface $message): void;

    public function GetOnlineCallsigns(): Collection;
}
