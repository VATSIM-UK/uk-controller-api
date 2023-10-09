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

    private string $aerodromeReferenceCode = 'aerodrome_reference_code ASC';
    private string $assignmentPriority = 'assignment_priority ASC';
    private string $otherStandRequests = 'other_stand_requests.id ASC';
    private string $random = 'RAND() ASC';

    private array $commonOrderByConditions = [
        $this->aerodromeReferenceCode,
        $this->assignmentPriority,
        $this->otherStandRequests,
        $this->random,
    ];

    private array $commonOrderByConditionsWithoutAssignmentPriority = [
        $this->aerodromeReferenceCode,
        $this->otherStandRequests,
        $this->random,
    ];

    private array $commonOrderByConditionsForRanking = [
        $this->aerodromeReferenceCode,
        $this->assignmentPriority,
    ];

    private array $commonOrderByConditionsWithoutAssignmentPriorityForRanking = [
        $this->aerodromeReferenceCode,
    ];
}
