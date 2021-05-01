<?php

namespace App\Models\Release\Departure;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\Pivot;

class ControllerDepartureReleaseDecision extends Pivot
{
    public $incrementing = true;

    /**
     * Approve the departure release for a given amount of time.
     */
    public function approve(int $userId, int $expiresInSeconds)
    {
        $this->update(
            [
                'released_by' => $userId,
                'released_at' => Carbon::now(),
                'release_expires_at' => Carbon::now()->addSeconds($expiresInSeconds),
            ]
        );
    }

    /**
     * Reject the departure release
     */
    public function reject(int $userId)
    {
        $this->update(
            [
                'rejected_by' => $userId,
                'rejected_at' => Carbon::now(),
            ]
        );
    }

    /**
     * Acknowledge the departure release but take no further
     * action.
     */
    public function acknowledge(int $userId)
    {
        $this->update(
            [
                'acknowledged_by' => $userId,
                'acknowledged_at' => Carbon::now(),
            ]
        );
    }
}
