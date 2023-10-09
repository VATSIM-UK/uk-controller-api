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

    private string $AERODROME_REFERENCE_CODE = 'aerodrome_reference_code ASC';
    private string $ASSIGNMENT_PRIORITY = 'assignment_priority ASC';
    private string $OTHER_STAND_REQUESTS = 'other_stand_requests.id ASC';
    private string $RANDOM = 'RAND() ASC';

    private array $commonOrderByConditions = [
        $this->AERODROME_REFERENCE_CODE,
        $this->ASSIGNMENT_PRIORITY,
        $this->OTHER_STAND_REQUESTS,
        $this->RANDOM,
    ];

    private array $commonOrderByConditionsWithoutAssignmentPriority = [
        $this->AERODROME_REFERENCE_CODE,
        $this->OTHER_STAND_REQUESTS,
        $this->RANDOM,
    ];

    private array $commonOrderByConditionsForRanking = [
        $this->AERODROME_REFERENCE_CODE,
        $this->ASSIGNMENT_PRIORITY,
    ];

    private array $commonOrderByConditionsWithoutAssignmentPriorityForRanking = [
        $this->AERODROME_REFERENCE_CODE,
    ];
}
