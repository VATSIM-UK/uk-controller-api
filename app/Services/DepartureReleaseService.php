<?php

namespace App\Services;

use App\Events\DepartureReleaseAcknowledgedEvent;
use App\Events\DepartureReleaseApprovedEvent;
use App\Events\DepartureReleaseRejectedEvent;
use App\Events\DepartureReleaseRequestCancelledEvent;
use App\Events\DepartureReleaseRequestedEvent;
use App\Exceptions\Release\Departure\DepartureReleaseAlreadyDecidedException;
use App\Exceptions\Release\Departure\DepartureReleaseDecisionNotAllowedException;
use Carbon\Carbon;
use App\Models\Release\Departure\DepartureReleaseRequest;
use Carbon\CarbonImmutable;

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
     * @throws DepartureReleaseDecisionNotAllowedException|DepartureReleaseAlreadyDecidedException
     */
    public function approveReleaseRequest(
        DepartureReleaseRequest $request,
        int $approvingControllerId,
        int $approvingUserId,
        ?int $approvalExpiresInSeconds,
        CarbonImmutable $releaseValidFrom,
        string $remarks
    ): void {
        $this->checkDecisionAllowed($request, $approvingControllerId, 'approve');

        $request->approve($approvingUserId, $approvalExpiresInSeconds, $releaseValidFrom, $remarks);
        event(new DepartureReleaseApprovedEvent($request));
    }

    /**
     * Reject a departure release on behalf of a single controller.
     * @throws DepartureReleaseDecisionNotAllowedException|DepartureReleaseAlreadyDecidedException
     */
    public function rejectReleaseRequest(
        DepartureReleaseRequest $request,
        int $rejectingControllerId,
        int $rejectingUserId,
        string $remarks
    ): void {
        $this->checkDecisionAllowed($request, $rejectingControllerId, 'reject');

        $request->reject($rejectingUserId, $remarks);
        event(new DepartureReleaseRejectedEvent($request));
    }

    /**
     * Acknowledge a departure release as received on behalf of a single controller
     * @throws DepartureReleaseDecisionNotAllowedException|DepartureReleaseAlreadyDecidedException
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

    /**
     * Acknowledge a departure release as received on behalf of a single controller
     * @throws DepartureReleaseDecisionNotAllowedException
     */
    public function cancelReleaseRequest(
        DepartureReleaseRequest $request,
        int $cancellingUserId
    ): void {
        if ($request->user_id !== $cancellingUserId) {
            throw new DepartureReleaseDecisionNotAllowedException(
                sprintf('Controller id %d cannot cancel this release', $cancellingUserId)
            );
        }
        $request->cancel();
        event(new DepartureReleaseRequestCancelledEvent($request));
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

        if ($request->decisionMade()) {
            throw new DepartureReleaseAlreadyDecidedException(
                sprintf('Cannot %s release id %d, decision already made', $action, $request->id)
            );
        }
    }

    public function cancelReleasesForAirborneAircraft(): void
    {
        $requestsToProcess = DepartureReleaseRequest::query()
            ->join('network_aircraft', 'network_aircraft.callsign', '=', 'departure_release_requests.callsign')
            ->where('network_aircraft.groundspeed', '>=', 50)
            ->where('network_aircraft.altitude', '>=', 1000)
            ->select('departure_release_requests.*')
            ->get();

        $requestsToProcess->each(function (DepartureReleaseRequest $request) {
            $request->delete();
            event(new DepartureReleaseRequestCancelledEvent($request));
        });
    }
}
