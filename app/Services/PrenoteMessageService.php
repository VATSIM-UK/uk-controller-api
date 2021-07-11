<?php

namespace App\Services;

use App\Events\Prenote\NewPrenoteMessageEvent;
use App\Events\Prenote\PrenoteAcknowledgedEvent;
use App\Events\Prenote\PrenoteDeletedEvent;
use App\Exceptions\Prenote\PrenoteAcknowledgementNotAllowedException;
use App\Exceptions\Prenote\PrenoteAlreadyAcknowledgedException;
use App\Exceptions\Prenote\PrenoteCancellationNotAllowedException;
use App\Helpers\Prenote\CreatePrenoteMessageData;
use App\Models\Prenote\PrenoteMessage;
use Carbon\Carbon;

class PrenoteMessageService
{
    public function createPrenoteMessage(CreatePrenoteMessageData $data): int
    {
        $prenoteMessage = PrenoteMessage::create(
            [
                'callsign' => $data->getCallsign(),
                'departure_airfield' => $data->getDepartureAirfield(),
                'departure_sid' => $data->getDepartureSid(),
                'destination_airfield' => $data->getDestinationAirfield(),
                'user_id' => $data->getUserId(),
                'controller_position_id' => $data->getRequestingControllerId(),
                'target_controller_position_id' => $data->getTargetControllerId(),
                'expires_at' => Carbon::now()->addSeconds($data->getExpiresInSeconds()),
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

    public function cancelMessagesForAirborneAircraft(): void
    {
        $messagesToProcess = PrenoteMessage::query()
            ->join('network_aircraft', 'network_aircraft.callsign', '=', 'prenote_messages.callsign')
            ->where('network_aircraft.groundspeed', '>=', 50)
            ->where('network_aircraft.altitude', '>=', 1000)
            ->select('prenote_messages.*')
            ->get();

        $messagesToProcess->each(function (PrenoteMessage $message) {
            $message->delete();
            event(new PrenoteDeletedEvent($message));
        });
    }
}
