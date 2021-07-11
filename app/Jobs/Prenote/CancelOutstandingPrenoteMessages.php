<?php

namespace App\Jobs\Prenote;

use App\Events\Prenote\PrenoteDeletedEvent;
use App\Jobs\Network\AircraftDisconnectedSubtask;
use App\Models\Prenote\PrenoteMessage;
use App\Models\Vatsim\NetworkAircraft;

class CancelOutstandingPrenoteMessages implements AircraftDisconnectedSubtask
{
    public function perform(NetworkAircraft $aircraft): void
    {
        PrenoteMessage::where('callsign', $aircraft->callsign)->each(
            function (PrenoteMessage $message) {
                $message->delete();
                event(new PrenoteDeletedEvent($message));
            }
        );
    }
}
