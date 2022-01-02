<?php

namespace App\Exceptions\Stand;

use InvalidArgumentException;

class StandReservationMissingMetadataException extends InvalidArgumentException
{
    public function __construct()
    {
        parent::__construct('Stand reservations require either a CID or Origin/Destination pair');
    }
}
