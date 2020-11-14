<?php

namespace App\Models\Vatsim;

use App\Models\Hold\AssignedHold;
use App\Models\Stand\Stand;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Location\Coordinate;

class NetworkAircraft extends Model
{
    const AIRCRAFT_TYPE_SEPARATOR = '/';

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

    protected $dates = [
        'transponder_last_updated_at',
    ];

    protected $casts = [
        'latitude' => 'float',
        'longitude' => 'float',
    ];

    public static function boot()
    {
        parent::boot();
        static::creating(function (NetworkAircraft $aircraft) {
            $aircraft->setUpdatedAt(Carbon::now());
            $aircraft->transponder_last_updated_at = Carbon::now();
        });

        static::updating(function (NetworkAircraft $aircraft) {
            if ($aircraft->isDirty('transponder')) {
                $aircraft->transponder_last_updated_at = Carbon::now();
            }
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

    public function getSquawkAttribute(): string
    {
        return $this->attributes['transponder'];
    }

    public function getLatLongAttribute(): Coordinate
    {
        return new Coordinate($this->latitude, $this->longitude);
    }

    public function squawkingMayday(): bool
    {
        return $this->attributes['transponder'] === '7700';
    }

    public function squawkingRadioFailure(): bool
    {
        return $this->attributes['transponder'] === '7600';
    }

    public function squawkingBannedSquawk(): bool
    {
        return $this->attributes['transponder'] === '7500';
    }

    public function occupiedStand(): BelongsToMany
    {
        return $this->belongsToMany(
            Stand::class,
            'aircraft_stand',
            'callsign',
            'stand_id'
        )->withTimestamps();
    }

    public function getAircraftTypeAttribute(): string
    {
        $splitType = explode(self::AIRCRAFT_TYPE_SEPARATOR, $this->planned_aircraft);
        switch (count($splitType)) {
            case 1:
            case 2:
                return $splitType[0];
            case 3:
            default:
                return $splitType[1];
        }
    }
}
