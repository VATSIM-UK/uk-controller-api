<?php

namespace App\Services\Stand;

use App\Models\Stand\StandAssignment;
use App\Models\Stand\StandAssignmentsHistory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StandAssignmentsHistoryService
{
    public function deleteHistoryFor(StandAssignment $target): void
    {
        StandAssignmentsHistory::where(
            'callsign',
            $target->callsign
        )->delete();
    }

    public function createHistoryItem(StandAssignment $assignment): void
    {
        DB::transaction(function () use ($assignment) {
            $this->deleteHistoryFor($assignment);
            StandAssignmentsHistory::create(
                [
                    'callsign' => $assignment->callsign,
                    'stand_id' => $assignment->stand_id,
                    'user_id' => !is_null(Auth::user()) ? Auth::user()->id : null,
                ]
            );
        });
    }
}
