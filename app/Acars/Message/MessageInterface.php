<?php

namespace App\Acars\Message;

interface MessageInterface
{
    /**
     * Returns the intended target of the ACARS message.
     */
    public function getTarget(): string;
}
