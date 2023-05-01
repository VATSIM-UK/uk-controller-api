<?php

namespace App\Listeners\Stand;

use App\Acars\Exception\AcarsRequestException;
use App\Acars\Message\Telex\StandAssignedTelexMessage;
use App\Acars\Provider\AcarsProviderInterface;
use App\Events\StandAssignedEvent;
use App\Models\Controller\ControllerPosition;
use App\Models\Stand\StandAssignment;
use App\Models\Vatsim\NetworkControllerPosition;
use App\Services\LocationService;
use Illuminate\Support\Facades\Log;
use Location\Distance\Haversine;

class SendStandAllocationAcarsMessage
{
    private const MIN_ASSIGNMENT_DISTANCE_NAUTICAL_MILES = 7.5;

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
            Log::error(
                sprintf('Acars exception sending stand allocation message: %s', $requestException->getMessage())
            );
        }
    }

    private function meetsSendingConditions(StandAssignment $assignment): bool
    {
        return $this->aircraftIsNotSweatbox($assignment) &&
            $this->standIsAtArrivalAirport($assignment) &&
            $this->notArrivingAndDepartingSameAirport($assignment) &&
            $this->aircraftIsNotTooCloseToDestination($assignment) &&
            $this->airfieldIsControlledOrUserAllowsMessagesForUncontrolledAirfields($assignment);
    }

    /**
     * For now, we know an aircraft isn't Sweatbox if we have a lat/long for it.
     */
    private function aircraftIsNotSweatbox(StandAssignment $assignment): bool
    {
        return isset($assignment->aircraft->latitude, $assignment->aircraft->longitude);
    }

    private function aircraftIsNotTooCloseToDestination(StandAssignment $assignment): bool
    {
        $aircraft = $assignment->aircraft;
        $airfield = $assignment->stand->airfield;
        return $airfield && $aircraft && LocationService::metersToNauticalMiles(
            $airfield->coordinate->getDistance(
                $aircraft->latLong,
                new Haversine()
            )
        ) > self::MIN_ASSIGNMENT_DISTANCE_NAUTICAL_MILES;
    }

    private function notArrivingAndDepartingSameAirport(StandAssignment $assignment): bool
    {
        return $assignment->aircraft->planned_depairport !== $assignment->aircraft->planned_destairport;
    }

    private function standIsAtArrivalAirport(StandAssignment $assignment): bool
    {
        return $assignment->stand->airfield->code === $assignment->aircraft->planned_destairport;
    }

    private function standAcarsMessagesAreEnabled(): bool
    {
        return config('stands.assignment_acars_message') === true;
    }

    private function userAllowsStandAllocationMessages(StandAssignment $standAssignment): bool
    {
        return (bool)$standAssignment->aircraft?->user?->send_stand_acars_messages;
    }

    private function airfieldIsControlledOrUserAllowsMessagesForUncontrolledAirfields(StandAssignment $assignment): bool
    {
        if ($assignment->aircraft?->user?->stand_acars_messages_uncontrolled_airfield) {
            return true;
        }

        $airfield = $assignment->stand->airfield;
        return NetworkControllerPosition::whereIn(
            'controller_position_id',
            $airfield->controllers->reject(fn (ControllerPosition $position) => $position->isDelivery())->pluck('id')
        )->exists();
    }
}
