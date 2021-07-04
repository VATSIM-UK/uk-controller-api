<?php

namespace App\Services;

use App\Events\Prenote\NewPrenoteMessageEvent;
use App\Events\Prenote\PrenoteAcknowledgedEvent;
use App\Events\Prenote\PrenoteDeletedEvent;
use App\Exceptions\Prenote\PrenoteAcknowledgementNotAllowedException;
use App\Exceptions\Prenote\PrenoteAlreadyAcknowledgedException;
use App\Exceptions\Prenote\PrenoteCancellationNotAllowedException;
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
    ): int {
        $prenoteMessage = PrenoteMessage::create(
            [
                'callsign' => $callsign,
                'departure_airfield' => $departureAirfield,
                'departure_sid' => $departureSid,
                'destination_airfield' => $destinationAirfield,
                'user_id' => $userId,
                'controller_position_id' => $sendingControllerId,
                'target_controller_position_id' => $targetControllerId,
                'expires_at' => Carbon::now()->addSeconds($expiresInSeconds),
            ]
        );

        event(new NewPrenoteMessageEvent($prenoteMessage));
        return $prenoteMessage->id;
    }

    public function acknowledgePrenoteMessage(
        PrenoteMessage $message,
        int $userId,
        int $controllerId
    ): void {
        if ($message->target_controller_position_id !== $controllerId) {
            throw new PrenoteAcknowledgementNotAllowedException(
                sprintf('Controller id %d cannot acknowledge this prenote', $controllerId)
            );
        }

        if ($message->acknowledged()) {
            throw new PrenoteAlreadyAcknowledgedException('This prenote is already acknowledged');
        }

        $message->acknowledge($userId);
        event(new PrenoteAcknowledgedEvent($message));
    }

    public function cancelPrenoteMessage(
        PrenoteMessage $message,
        int $userId
    ): void {
        if ($message->user_id !== $userId) {
            throw new PrenoteCancellationNotAllowedException(
                sprintf('User id %d cannot acknowledge this prenote', $userId)
            );
        }

        $message->delete();
        event(new PrenoteDeletedEvent($message));
    }
}
