<?php

namespace App\Listeners\Stand;

use App\Acars\Exception\AcarsRequestException;
use App\Acars\Message\Telex\StandAssignedTelexMessage;
use App\Acars\Provider\AcarsProviderInterface;
use App\Events\StandAssignedEvent;
use App\Models\Stand\StandAssignment;
use Illuminate\Support\Facades\Log;
use Location\Distance\Haversine;

class SendStandAllocationAcarsMessage
{
    private const MIN_ASSIGNMENT_DISTANCE = 7.5;

    private readonly AcarsProviderInterface $acarsProvider;

    public function __construct(AcarsProviderInterface $acarsProvider)
    {
        $this->acarsProvider = $acarsProvider;
    }

    public function handle(StandAssignedEvent $assignedEvent)
    {
        if (!$this->standAcarsMessagesAreEnabled()) {
            return;
        }

        $standAssignment = $assignedEvent->getStandAssignment();
        if (!$this->userAllowsStandAllocationMessages($standAssignment)) {
            return;
        }

        if (!$this->meetsSendingConditions($standAssignment)) {
            return;
        }

        try {
            $this->acarsProvider->sendTelex(new StandAssignedTelexMessage($standAssignment));
        } catch (AcarsRequestException $requestException) {
            Log::error(sprintf('Acars exception sending stand allocation message: %s', $requestException->getMessage()));
        }
    }

    private function meetsSendingConditions(StandAssignment $assignment): bool
    {
        return $this->standIsAtArrivalAirport($assignment) &&
            $this->notArrivingAndDepartingSameAirport($assignment) &&
            $this->aircraftIsNotTooCloseToDestination($assignment);
    }

    private function aircraftIsNotTooCloseToDestination(StandAssignment $assignment): bool
    {
        $aircraft = $assignment->aircraft;
        $airfield = $assignment->aifield;
        return $airfield && $aircraft && $airfield->coordinate->getDistance(
            $aircraft->latLong,
                new Haversine()
        ) > self::MIN_ASSIGNMENT_DISTANCE;
    }

    private function notArrivingAndDepartingSameAirport(StandAssignment $assignment): bool
    {
        return $assignment->aircraft->planned_depairport !== $assignment->aircraft->planned_destairport;
    }

    private function standIsAtArrivalAirport(StandAssignment $assignment): bool
    {
        return $assignment->stand->airport->code === $assignment->aircraft->planned_destairport;
    }

    private function standAcarsMessagesAreEnabled(): bool
    {
        return config('assignment_acars_message') === true;
    }

    private function userAllowsStandAllocationMessages(StandAssignment $standAssignment): bool
    {
        return $standAssignment->aircraft?->user !== null;
    }
}
