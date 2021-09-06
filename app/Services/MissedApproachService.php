<?php

namespace App\Services;

use App\Events\MissedApproach\MissedApproachEvent;
use App\Exceptions\MissedApproach\MissedApproachAlreadyActiveException;
use App\Models\MissedApproach\MissedApproachNotification;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class MissedApproachService
{
    const MESSAGE_ACTIVE_MINUTES = 3;

    public function sendMissedApproachNotification(string $callsign): void
    {
        if ($this->missedApproachActive($callsign)) {
            throw new MissedApproachAlreadyActiveException('Missed approach already active for ' . $callsign);
        }

        event(
            new MissedApproachEvent(
                MissedApproachNotification::create(
                    [
                        'callsign' => $callsign,
                        'user_id' => Auth::id(),
                        'expires_at' => Carbon::now()->addMinutes(self::MESSAGE_ACTIVE_MINUTES)
                    ]
                )
            )
        );
    }

    private function missedApproachActive(string $callsign): bool
    {
        return MissedApproachNotification::where('callsign', $callsign)
            ->where('expires_at', '>', Carbon::now())
            ->exists();
    }
}
