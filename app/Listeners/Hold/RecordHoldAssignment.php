<?php

namespace App\Listeners\Hold;

use App\Events\HoldAssignedEvent;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RecordHoldAssignment
{
    public function handle(HoldAssignedEvent $allocationEvent) : bool
    {
        DB::table('assigned_holds_history')
            ->insert(
                [
                    'callsign' => $allocationEvent->getHold()->callsign,
                    'navaid_id' => $allocationEvent->getHold()->navaid_id,
                    'assigned_by' => Auth::user()->id,
                    'assigned_at' => $allocationEvent->getHold()->updated_at ?? $allocationEvent->getHold()->created_at,
                ]
            );

        return true;
    }
}
