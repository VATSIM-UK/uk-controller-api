<?php

namespace App\Models\Release\Departure;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\Pivot;

class ControllerDepartureReleaseDecision extends Pivot
{
    public $incrementing = true;

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

    public function reject(int $userId)
    {
        $this->update(
            [
                'rejected_by' => $userId,
                'rejected_at' => Carbon::now(),
            ]
        );
    }
}
