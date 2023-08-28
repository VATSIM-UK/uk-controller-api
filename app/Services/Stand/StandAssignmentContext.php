<?php

namespace App\Services\Stand;

use App\Models\Stand\StandAssignment;
use Illuminate\Support\Collection;

class StandAssignmentContext
{
    private readonly StandAssignment $assignment;

    private readonly string $assignmentType;

    private readonly Collection $removedAssignments;

    public function __construct(StandAssignment $assignment, string $assignmentType, Collection $removedAssignments)
    {
        $this->assignment = $assignment;
        $this->assignmentType = $assignmentType;
        $this->removedAssignments = $removedAssignments;
    }

    public function assignment(): StandAssignment
    {
        return $this->assignment;
    }

    public function assignmentType(): string
    {
        return $this->assignmentType;
    }

    public function removedAssignments(): Collection
    {
        return $this->removedAssignments;
    }
}
