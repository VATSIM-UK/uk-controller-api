<?php

namespace App\Services;

use App\Events\DepartureReleaseAcknowledgedEvent;
use App\Events\DepartureReleaseApprovedEvent;
use App\Events\DepartureReleaseRejectedEvent;
use App\Events\DepartureReleaseRequestedEvent;
use App\Exceptions\Release\Departure\DepartureReleaseDecisionNotAllowedException;
use Carbon\Carbon;
use App\Models\Release\Departure\DepartureReleaseRequest;
use Carbon\CarbonImmutable;
use Exception;

class DepartureReleaseService
{
    /**
     * Request a departure release from the given controllers.
     */
    public function makeReleaseRequest(
        string $callsign,
        int $requestingUserId,
        int $requestingController,
        int $targetController,
        int $expiresInSeconds
    ): int {
        $releaseRequest = DepartureReleaseRequest::create(
            [
                'callsign' => $callsign,
                'user_id' => $requestingUserId,
                'controller_position_id' => $requestingController,
                'target_controller_position_id' => $targetController,
                'expires_at' => Carbon::now()->addSeconds($expiresInSeconds)
            ]
        );

        event(new DepartureReleaseRequestedEvent($releaseRequest));
        return $releaseRequest->id;
    }

    /**
     * Approve a departure release on behalf of a single controller.
     * @throws DepartureReleaseDecisionNotAllowedException
     */
    public function approveReleaseRequest(
        DepartureReleaseRequest $request,
        int $approvingControllerId,
        int $approvingUserId,
        int $approvalExpiresInSeconds,
        CarbonImmutable $releaseValidFrom
    ): void {
        $this->checkDecisionAllowed($request, $approvingControllerId, 'approve');

        $request->approve($approvingUserId, $approvalExpiresInSeconds, $releaseValidFrom);
        event(new DepartureReleaseApprovedEvent($request));
    }

    /**
     * Reject a departure release on behalf of a single controller.
     * @throws DepartureReleaseDecisionNotAllowedException
     */
    public function rejectReleaseRequest(
        DepartureReleaseRequest $request,
        int $rejectingControllerId,
        int $rejectingUserId
    ): void {
        $this->checkDecisionAllowed($request, $rejectingControllerId, 'reject');

        $request->reject($rejectingUserId);
        event(new DepartureReleaseRejectedEvent($request));
    }

    /**
     * Acknowledge a departure release as received on behalf of a single controller
     * @throws DepartureReleaseDecisionNotAllowedException
     */
    public function acknowledgeReleaseRequest(
        DepartureReleaseRequest $request,
        int $acknowledgingControllerId,
        int $acknowledgingUserId
    ): void {
        $this->checkDecisionAllowed($request, $acknowledgingControllerId, 'acknowledge');

        $request->acknowledge($acknowledgingUserId);
        event(new DepartureReleaseAcknowledgedEvent($request));
    }

    private function checkDecisionAllowed(
        DepartureReleaseRequest $request,
        int $decisionControllerId,
        string $action
    ): void {
        if ($request->target_controller_position_id !== $decisionControllerId) {
            throw new DepartureReleaseDecisionNotAllowedException(
                sprintf('Controller id %d cannot %s this release', $decisionControllerId, $action)
            );
        }
    }
}
