<?php

namespace App\Allocator\Stand;

/**
 * Specifies the common conditions for ordering stands.
 *
 * Any class that uses this trait must also use the ConsidersStandRequests trait and call
 * joinOtherStandRequests() on the query before applying the ordering.
 *
 */
trait OrdersStandsByCommonConditions
{
    use ConsidersStandRequests;

    const AERODROME_REFERENCE_CODE = 'aerodrome_reference_code ASC';
    const ASSIGNMENT_PRIORITY = 'assignment_priority ASC';
    const OTHER_STAND_REQUESTS = 'other_stand_requests.id ASC';
    const RANDOM = 'RAND() ASC';

    private array $commonOrderByConditions = [
        self::AERODROME_REFERENCE_CODE,
        self::ASSIGNMENT_PRIORITY,
        self::OTHER_STAND_REQUESTS,
        self::RANDOM,
    ];

    private array $commonOrderByConditionsWithoutAssignmentPriority = [
        self::AERODROME_REFERENCE_CODE,
        self::OTHER_STAND_REQUESTS,
        self::RANDOM,
    ];

    private array $commonOrderByConditionsForRanking = [
        self::AERODROME_REFERENCE_CODE,
        self::ASSIGNMENT_PRIORITY,
    ];

    private array $commonOrderByConditionsWithoutAssignmentPriorityForRanking = [
        self::AERODROME_REFERENCE_CODE,
    ];
}
