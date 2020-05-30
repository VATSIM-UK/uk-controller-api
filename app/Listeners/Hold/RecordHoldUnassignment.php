<?php

namespace App\Listeners\Hold;

use App\Events\HoldAssignedEvent;
use App\Events\HoldUnassignedEvent;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RecordHoldUnassignment
{
    public function handle(HoldUnassignedEvent $allocationEvent) : bool
    {
        DB::table('assigned_holds_history')
            ->insert(
                [
                    'callsign' => $allocationEvent->getHold()->callsign,
                    'navaid_id' => null,
                    'assigned_by' => Auth::user()->id,
                    'assigned_at' => $allocationEvent->getHold()->updated_at ?? $allocationEvent->getHold()->created_at,
                ]
        );

        return false;
    }
}
