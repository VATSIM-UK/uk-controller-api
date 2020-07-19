<?php

namespace App\Listeners\GroundStatus;

use App\Events\GroundStatusAssignedEvent;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RecordGroundStatusHistory
{
    public function handle(GroundStatusAssignedEvent $event) : bool
    {
        DB::table('ground_status_history')
            ->insert(
                [
                    'callsign' => $event->getCallsign(),
                    'ground_status_id' => $event->getGroundStatusId(),
                    'user_id' => !is_null(Auth::user()) ? Auth::user()->id : null,
                    'assigned_at' => Carbon::now(),
                ]
            );

        return true;
    }
}
