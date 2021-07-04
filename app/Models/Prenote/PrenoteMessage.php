<?php

namespace App\Models\Prenote;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PrenoteMessage extends Model
{
    use SoftDeletes;

    const UPDATED_AT = null;

    protected $fillable = [
        'callsign',
        'departure_airfield',
        'departure_sid',
        'destination_airfield',
        'user_id',
        'controller_position_id',
        'target_controller_position_id',
        'expires_at',
    ];

    protected $dates = [
        'created_at',
        'expires_at',
        'acknowledged_at',
    ];

    public function scopeTarget(Builder $query, int $targetController): Builder
    {
        return $query->where('target_controller_position_id', $targetController);
    }

    public function scopeActiveFor(Builder $query, string $callsign): Builder
    {
        return $query->where('callsign', $callsign)
            ->whereNull('acknowledged_at')
            ->where('expires_at', '>', Carbon::now());
    }

    public function acknowledged(): bool
    {
        return $this->acknowledged_at !== null;
    }

    public function acknowledge(int $userId): PrenoteMessage
    {
        $this->acknowledged_by = $userId;
        $this->acknowledged_at = Carbon::now();
        $this->save();
        return $this;
    }
}
