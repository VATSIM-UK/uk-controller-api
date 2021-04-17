<?php

namespace App\Services;

use App\Events\DepartureReleaseApprovedEvent;
use App\Events\DepartureReleaseRequestedEvent;
use App\Exceptions\Release\Departure\DepartureReleaseDecisionNotAllowedException;
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
    ): int
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
        return $releaseRequest->id;
    }

    public function approveReleaseRequest(
        DepartureReleaseRequest $request,
        int $approvingControllerId,
        int $approvingUserId,
        int $approvalExpiresInSeconds
    ): void {
        $controller = $request->controllerPositions()
            ->wherePivot('controller_position_id', $approvingControllerId)
            ->first();

        if (!$controller) {
            throw new DepartureReleaseDecisionNotAllowedException(
                sprintf('Controller id %d cannot approve this release', $approvingControllerId)
            );
        }

        $controller->decision->approve($approvingUserId, $approvalExpiresInSeconds);
        event(new DepartureReleaseApprovedEvent($controller->decision));
    }
}
