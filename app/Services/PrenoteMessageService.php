<?php

namespace App\Services;

use App\Events\Prenote\NewPrenoteMessageEvent;
use App\Models\Prenote\PrenoteMessage;
use Carbon\Carbon;

class PrenoteMessageService
{
    public function createPrenoteMessage(
        string $callsign,
        string $departureAirfield,
        ?string $departureSid,
        ?string $destinationAirfield,
        int $userId,
        int $sendingControllerId,
        int $targetControllerId,
        int $expiresInSeconds
    ): int
    {
        $prenoteMessage = PrenoteMessage::create(
            [
                'callsign' => $callsign,
                'departure_airfield' => $departureAirfield,
                'departure_sid' => $departureSid,
                'destination_airfield' => $destinationAirfield,
                'user_id' => $userId,
                'controller_position_id' => $sendingControllerId,
                'target_controller_position_id' => $targetControllerId,
                'expires_at' => Carbon::now()->addSeconds($expiresInSeconds)
            ]
        );

        event(new NewPrenoteMessageEvent($prenoteMessage));
        return $prenoteMessage->id;
    }
}
