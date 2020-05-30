<?php

namespace App\Listeners\Hold;

use App\Events\HoldAssignedEvent;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RecordHoldAssignment
{
    public function handle(HoldAssignedEvent $allocationEvent) : bool
    {
        DB::table('hold_assignment_history')
            ->insert(
                [
                    'callsign' => $allocationEvent->getHold()->callsign,
                    'navaid_id' => $allocationEvent->getHold()->navaid->id,
                    'assigned_by' => Auth::user()->id,
                    'assigned_at' => $allocationEvent->updated_at ?? $allocationEvent->created_at,
                ]
        );

        return false;
    }
}
