<?php
namespace App\Services;

use App\Events\SquawkAssignmentEvent;
use App\Helpers\Squawks\SquawkAllocation;
use App\Models\Squawks\Allocation;
use App\Models\Squawks\AllocationHistory;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Event;

/**
 * Service for managing squawk allocations in the database.
 * Also handles allocation history auditing.
 *
 * Class SquawkAllocationService
 * @package App\Services
 */
class SquawkAllocationService
{
    // How many months allocation history should be kept for
    const ALLOCATION_HISTORY_KEEP_MONTHS = 2;

    /**
     * Creates the squawk allocation in the database
     *
     * @param string $callsign The callsign the squawk is allocated to
     * @param string $squawk The squawk code to be assigned
     * @return SquawkAllocation
     */
    public function createOrUpdateAllocation(string $callsign, string $squawk) : SquawkAllocation
    {
        $allocation = Allocation::updateOrCreate(
            ['callsign' => $callsign],
            [
                'squawk' => $squawk,
                'callsign' => $callsign,
                'allocated_by' => Auth::user()->id,
            ]
        )->touchAllocated();

        Event::dispatch(new SquawkAssignmentEvent($allocation));
        return new SquawkAllocation($allocation->squawk, $allocation->wasRecentlyCreated);
    }

    /**
     * Deletes old history that we don't need to keep anymore.
     */
    public function deleteOldAuditHistory()
    {
        AllocationHistory::where(
            'allocated_at',
            '<',
            Carbon::now()->subMonths(self::ALLOCATION_HISTORY_KEEP_MONTHS)->toDateTimeString()
        )->delete();
    }
}
