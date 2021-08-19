<?php

namespace App\Helpers\Acars;

/*
 * Represents a telex message to be sent
 */
interface TelexMessageInterface
{
    /*
     * Who the message is intended for
     */
    public function getTarget(): string;

    /*
     * The content of the message
     */
    public function getMessage(): string;
}
