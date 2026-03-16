<?php

namespace App\Services\Stand;

use App\Models\Stand\StandAssignment;

class StandAssignmentPayload
{
    public const SOURCE_MANUAL = 'manual';
    public const SOURCE_RESERVATION_ALLOCATOR = 'reservation_allocator';
    public const SOURCE_VAA_ALLOCATOR = 'vaa_allocator';
    public const SOURCE_SYSTEM_AUTO = 'system_auto';

    public const STATUS_ASSIGNED = 'assigned';
    public const STATUS_REQUESTED_UNAVAILABLE = 'requested_unavailable';

    private const PILOT_REQUEST_ALLOCATOR_PREFIX = 'App\\Allocator\\Stand\\UserRequestedArrivalStandAllocator';

    public static function fromAssignment(StandAssignment $assignment): array
    {
        $source = self::sourceFromHistoryType((string) $assignment->assignmentHistory?->type);
        $assignedByPilotRequest = $source === self::SOURCE_RESERVATION_ALLOCATOR;

        return [
            'callsign' => $assignment->callsign,
            'stand_id' => $assignment->stand_id,
            'assigned_by_reservation_allocator' => $assignedByPilotRequest,
            'assigned_by_pilot_request' => $assignedByPilotRequest,
            'assignment_source' => $source,
            'assignment_status' => self::STATUS_ASSIGNED,
        ];
    }

    public static function requestedUnavailable(string $callsign): array
    {
        return [
            'callsign' => $callsign,
            'stand_id' => null,
            'assigned_by_reservation_allocator' => true,
            'assigned_by_pilot_request' => true,
            'assignment_source' => self::SOURCE_RESERVATION_ALLOCATOR,
            'assignment_status' => self::STATUS_REQUESTED_UNAVAILABLE,
        ];
    }

    private static function sourceFromHistoryType(string $historyType): string
    {
        if (str_starts_with($historyType, self::PILOT_REQUEST_ALLOCATOR_PREFIX)) {
            return self::SOURCE_RESERVATION_ALLOCATOR;
        }

        if ($historyType === 'User') {
            return self::SOURCE_MANUAL;
        }

        if (str_contains($historyType, 'Vaa') || str_contains($historyType, 'StandReservation')) {
            return self::SOURCE_VAA_ALLOCATOR;
        }

        return self::SOURCE_SYSTEM_AUTO;
    }
}
