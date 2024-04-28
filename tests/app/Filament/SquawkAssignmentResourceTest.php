<?php

namespace App\Filament;

use App\BaseFilamentTestCase;
use App\Filament\AccessCheckingHelpers\ChecksListingFilamentAccess;
use App\Filament\Resources\SquawkAssignmentResource;
use App\Models\Squawk\SquawkAssignment;

class SquawkAssignmentResourceTest extends BaseFilamentTestCase
{
    use ChecksListingFilamentAccess;

    protected function getIndexText(): array
    {
        return ['Squawk Assignments', 'BAW123', '1234', 'Not assigned by UKCP'];
    }

    protected static function resourceClass(): string
    {
        return SquawkAssignmentResource::class;
    }

    protected function beforeListing(): void
    {
        SquawkAssignment::create(
            [
                'callsign' => 'BAW123',
                'code' => '1234',
                'assignment_type' => 'NON_UKCP',
            ]
        );
    }
}
