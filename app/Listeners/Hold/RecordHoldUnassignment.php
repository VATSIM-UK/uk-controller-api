<?php

namespace App\Listeners\Hold;

use App\Events\HoldAssignedEvent;
use App\Events\HoldUnassignedEvent;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RecordHoldUnassignment
{
    public function handle(HoldUnassignedEvent $allocationEvent) : bool
    {
        DB::table('assigned_holds_history')
            ->insert(
                [
                    'callsign' => $allocationEvent->getCallsign(),
                    'navaid_id' => null,
                    'assigned_by' => !is_null(Auth::user()) ? Auth::user()->id : null,
                    'assigned_at' => Carbon::now(),
                ]
        );

        return true;
    }
}
