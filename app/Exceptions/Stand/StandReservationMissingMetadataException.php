<?php

namespace App\Exceptions\Stand;

use InvalidArgumentException;

class StandReservationMissingMetadataException extends InvalidArgumentException
{
    public function __construct()
    {
        parent::__construct('Stand reservations with a CID require an origin/destination pair');
    }
}
