<?php

namespace App\Models\Vatsim;

use App\Models\GroundStatus\GroundStatus;
use App\Models\Hold\AssignedHold;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class NetworkAircraft extends Model
{
    protected $primaryKey = 'callsign';

    public $incrementing = false;

    public $keyType = 'string';

    protected $fillable = [
        'callsign',
        'latitude',
        'longitude',
        'altitude',
        'groundspeed',
        'planned_aircraft',
        'planned_depairport',
        'planned_destairport',
        'planned_altitude',
        'transponder',
        'planned_flighttype',
        'planned_route',
    ];

    public static function boot()
    {
        parent::boot();
        static::creating(function (NetworkAircraft $aircraft) {
            $aircraft->setUpdatedAt(Carbon::now());
        });
    }

    public function assignedHold(): BelongsTo
    {
        return $this->belongsTo(AssignedHold::class, 'callsign', 'callsign');
    }

    public function firEvents(): HasMany
    {
        return $this->hasMany(NetworkAircraftFirEvent::class, 'callsign', 'callsign');
    }

    public function groundStatus(): BelongsToMany
    {
        return $this->belongsToMany(
            GroundStatus::class,
            'ground_status_network_aircraft',
            'callsign',
            'ground_status_id',
        );
    }
}
