<?php

namespace App\Acars\Provider;

use App\Acars\Exception\AcarsRequestException;
use App\Acars\Message\Telex\TelexMessageInterface;

interface AcarsProviderInterface
{
    /**
     * Sends a TELEX message.
     *
     * @throws AcarsRequestException
     */
    public function sendTelex(TelexMessageInterface $message): void;
}
