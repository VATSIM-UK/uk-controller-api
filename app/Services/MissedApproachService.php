<?php

namespace App\Services;

use App\Events\MissedApproach\MissedApproachAcknowledgedEvent;
use App\Events\MissedApproach\MissedApproachEvent;
use App\Exceptions\MissedApproach\CannotAcknowledgeMissedApproachException;
use App\Exceptions\MissedApproach\MissedApproachAlreadyActiveException;
use App\Models\Controller\ControllerPosition;
use App\Models\MissedApproach\MissedApproachNotification;
use App\Models\Vatsim\NetworkAircraft;
use App\Models\Vatsim\NetworkControllerPosition;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class MissedApproachService
{
    const MESSAGE_ACTIVE_MINUTES = 3;

    public function sendMissedApproachNotification(string $callsign): MissedApproachNotification
    {
        if ($this->missedApproachActive($callsign)) {
            throw new MissedApproachAlreadyActiveException('Missed approach already active for ' . $callsign);
        }

        return tap(
            MissedApproachNotification::create(
                [
                    'callsign' => $callsign,
                    'user_id' => Auth::id(),
                    'expires_at' => Carbon::now()->addMinutes(self::MESSAGE_ACTIVE_MINUTES)
                ]
            ),
            function (MissedApproachNotification $notification) {
                event(new MissedApproachEvent($notification));
            }
        );
    }

    public function acknowledge(MissedApproachNotification $missedApproachNotification, string $remarks)
    {
        if ($missedApproachNotification->acknowledged()) {
            Log::warning(
                sprintf('User %d is trying to acknowledge a missed approach but is already done', Auth::id())
            );
            throw new CannotAcknowledgeMissedApproachException();
        }

        // Check if they're on a position that's allowed to.
        $userPosition = NetworkControllerPosition::with('controllerPosition')
            ->whereHas('controllerPosition')
            ->where('cid', Auth::id())
            ->first();

        if (!$userPosition) {
            Log::warning(
                sprintf('User %d is trying to acknowledge a missed approach but is not controlling', Auth::id())
            );
            throw new CannotAcknowledgeMissedApproachException();
        }

        if (!$this->userPositionCanAcknowledge($missedApproachNotification, $userPosition->controllerPosition)) {
            Log::warning(
                sprintf(
                    'User %d is trying to acknowledge a missed approach but is not on a position that can',
                    Auth::id()
                )
            );
            throw new CannotAcknowledgeMissedApproachException();
        }

        $missedApproachNotification->acknowledge(Auth::id(), $remarks);
        event(new MissedApproachAcknowledgedEvent($missedApproachNotification, $userPosition->callsign));
    }

    private function missedApproachActive(string $callsign): bool
    {
        return MissedApproachNotification::where('callsign', $callsign)
            ->where('expires_at', '>', Carbon::now())
            ->exists();
    }

    private function userPositionCanAcknowledge(
        MissedApproachNotification $missedApproach,
        ControllerPosition $controllerPosition
    ): bool {
        return ($controllerPosition->isApproach() || $controllerPosition->isEnroute()) &&
            AirfieldService::controllerIsInTopDownOrder(
                $controllerPosition,
                NetworkAircraft::find($missedApproach->callsign)->planned_destairport
            );
    }
}
