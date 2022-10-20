<?php

namespace App\Models\Vatsim;

use App\Models\Airfield\Airfield;
use App\Models\Hold\NavaidNetworkAircraft;
use App\Models\Navigation\Navaid;
use App\Models\Stand\Stand;
use App\Models\Stand\StandAssignment;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Location\Coordinate;

class NetworkAircraft extends Model
{
    use HasFactory;

    private const TIME_OUT_MINUTES = 20;

    const AIRCRAFT_TYPE_REGEX = '/^[0-9A-Z]{4}/';

    const AIRCRAFT_TYPE_SEPARATOR = '/';

    protected $primaryKey = 'callsign';

    public $incrementing = false;

    public $keyType = 'string';

    protected $fillable = [
        'callsign',
        'cid',
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
        'remarks',
    ];

    protected $dates = [
        'transponder_last_updated_at',
    ];

    protected $casts = [
        'cid' => 'integer',
        'latitude' => 'float',
        'longitude' => 'float',
    ];

    public static function boot()
    {
        parent::boot();
        static::creating(function (NetworkAircraft $aircraft) {
            $aircraft->setUpdatedAt(Carbon::now());
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
        )->withPivot('aircraft_stand.id', 'latitude', 'longitude', 'updated_at');
    }

    public function assignedStand(): HasOne
    {
        return $this->hasOne(StandAssignment::class, 'callsign', 'callsign');
    }

    public function proximityNavaids(): BelongsToMany
    {
        return $this->belongsToMany(
            Navaid::class,
            'navaid_network_aircraft',
            'callsign',
            'navaid_id'
        )->withPivot('entered_at')->using(NavaidNetworkAircraft::class);
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

    public function scopeTimedOut(Builder $builder): Builder
    {
        return $builder->where('network_aircraft.updated_at', '<', Carbon::now()->subMinutes(self::TIME_OUT_MINUTES));
    }

    public function scopeNotTimedOut(Builder $builder): Builder
    {
        return $builder->where('network_aircraft.updated_at', '>', Carbon::now()->subMinutes(self::TIME_OUT_MINUTES));
    }

    public function destinationAirfield(): BelongsTo
    {
        return $this->belongsTo(
            Airfield::class,
            'planned_destairport',
            'code'
        );
    }
}
