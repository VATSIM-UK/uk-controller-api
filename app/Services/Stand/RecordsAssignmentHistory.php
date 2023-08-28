<?php

namespace App\Services\Stand;

use App\Models\Stand\StandAssignment;

interface RecordsAssignmentHistory
{

    /**
     * Deletes any history items for the given assignment.
     */
    public function deleteHistoryFor(StandAssignment $target): void;

    /**
     * Creates a history item for the given assignment, along with some context about the assignment.
     */
    public function createHistoryItem(StandAssignmentContext $context): void;
}
