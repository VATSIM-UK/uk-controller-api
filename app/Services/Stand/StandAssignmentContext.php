<?php

namespace App\Services\Stand;

use App\Models\Stand\StandAssignment;
use App\Models\Vatsim\NetworkAircraft;
use Illuminate\Support\Collection;

class StandAssignmentContext
{
    public function __construct(
        public readonly StandAssignment $assignment,
        public readonly string $assignmentType,
        public readonly Collection $removedAssignments,
        public readonly NetworkAircraft $aircraft
    ) {
    }
}
