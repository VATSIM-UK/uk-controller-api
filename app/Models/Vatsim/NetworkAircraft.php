<?php

namespace App\Models\Vatsim;

use App\Models\Stand\Stand;
use App\Models\Stand\StandAssignment;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Location\Coordinate;

class NetworkAircraft extends Model
{
    use HasFactory;

    const AIRCRAFT_TYPE_REGEX = '/^[0-9A-Z]{4}/';

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
        )->withPivot('latitude', 'longitude', 'updated_at');
    }

    public function assignedStand(): HasOne
    {
        return $this->hasOne(StandAssignment::class, 'callsign', 'callsign');
    }

    public function getAircraftTypeAttribute(): string
    {
        if (is_null($this->planned_aircraft)) {
            return '';
        }

        $splitType = explode(self::AIRCRAFT_TYPE_SEPARATOR, $this->planned_aircraft);
        foreach ($splitType as $split) {
            if (preg_match(self::AIRCRAFT_TYPE_REGEX, $split)) {
                return $split;
            }
        }

        return '';
    }

    public function isVfr(): bool
    {
        return in_array($this->planned_flighttype, ['V', 'S']);
    }

    public function isIfr(): bool
    {
        return $this->planned_flighttype === 'I';
    }
}
