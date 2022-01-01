<?php

namespace App\Exceptions\Stand;

use InvalidArgumentException;

class StandReservationTimeInvalidException extends InvalidArgumentException
{
    public function __construct()
    {
        parent::__construct('Invalid stand reservation time');
    }
}
