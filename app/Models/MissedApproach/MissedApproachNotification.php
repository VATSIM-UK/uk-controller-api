<?php

namespace App\Models\MissedApproach;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class MissedApproachNotification extends Model
{
    const UPDATED_AT = null;

    protected $fillable = [
        'callsign',
        'user_id',
        'expires_at',
    ];

    protected $dates = [
        'created_at',
        'expires_at',
        'acknowledged_at',
    ];

    public function acknowledge(int $userId, string $remarks): void
    {
        $this->acknowledged_by = $userId;
        $this->acknowledged_at = Carbon::now();
        $this->remarks = $remarks;
        $this->save();
    }

    public function acknowledged(): bool
    {
        return $this->acknowledged_at !== null;
    }
}
