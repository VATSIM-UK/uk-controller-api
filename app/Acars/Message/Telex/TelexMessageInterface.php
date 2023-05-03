<?php

namespace App\Acars\Message\Telex;

use App\Acars\Message\MessageInterface;

interface TelexMessageInterface extends MessageInterface
{
    /**
     * Returns the body of the message.
     */
    public function getBody(): string;
}
