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

    private array $commonOrderByConditions = [
        'aerodrome_reference_code ASC',
        'assignment_priority ASC',
        'other_stand_requests.id ASC',
        'RAND() ASC',
    ];

    private array $commonOrderByConditionsWithoutAssignmentPriority = [
        'aerodrome_reference_code ASC',
        'other_stand_requests.id ASC',
        'RAND() ASC',
    ];
}
