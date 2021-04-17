<?php

namespace App\Services;

use App\Events\DepartureReleaseRequestedEvent;
use Carbon\Carbon;
use App\Models\Release\Departure\DepartureReleaseRequest;

class DepartureReleaseService
{
    public function makeReleaseRequest(
        string $callsign,
        int $requestingUserId,
        int $requestingController,
        array $targetControllers,
        int $expiresInSeconds
    ): void
    {
        $releaseRequest = DepartureReleaseRequest::create(
            [
                'callsign' => $callsign,
                'user_id' => $requestingUserId,
                'controller_position_id' => $requestingController,
                'expires_at' => Carbon::now()->addSeconds($expiresInSeconds)
            ]
        );

        $releaseRequest->controllerPositions()->sync($targetControllers);
        event(new DepartureReleaseRequestedEvent($releaseRequest));
    }
}
