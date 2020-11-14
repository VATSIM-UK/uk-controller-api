<?php

namespace App\Models\Stand;

use App\Models\Aircraft\WakeCategory;
use App\Models\Airfield\Airfield;
use App\Models\Airline\Airline;
use App\Models\Vatsim\NetworkAircraft;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Location\Coordinate;

class Stand extends Model
{
    protected $fillable = [
        'airfield_id',
        'identifier',
        'latitude',
        'longitude',
    ];

    protected $casts = [
        'latitude' => 'double',
        'longitude' => 'double',
    ];

    public function assignment(): HasOne
    {
        return $this->hasOne(StandAssignment::class);
    }

    public function airfield(): BelongsTo
    {
        return $this->belongsTo(Airfield::class);
    }

    public function airlines(): BelongsToMany
    {
        return $this->belongsToMany(
            Airline::class,
            'airline_stand',
            'stand_id',
            'airline_id'
        )->withPivot('destination')->withTimestamps();
    }

    public function getCoordinateAttribute()
    {
        return new Coordinate($this->latitude, $this->longitude);
    }

    public function scopeUnoccupied(Builder $builder): Builder
    {
        return $builder->whereDoesntHave('occupier');
    }

    public function occupier(): BelongsToMany
    {
        return $this->belongsToMany(
            NetworkAircraft::class,
            'aircraft_stand',
            'stand_id',
            'callsign'
        )->withTimestamps();
    }

    public function scopeUnassigned(Builder $builder): Builder
    {
        return $builder->whereDoesntHave('assignment');
    }

    public function scopeAvailable(Builder $builder): Builder
    {
        return $this->scopeUnassigned($this->scopeUnoccupied($builder));
    }

    public function scopeAirline(Builder $builder, Airline $airline): Builder
    {
        return $builder->whereHas('airlines', function (Builder $airlineQuery) use ($airline) {
            $airlineQuery->where('airlines.id', $airline->id);
        });
    }

    public function scopeAirlineDestination(Builder $builder, Airline $airline, array $destinationStrings): Builder
    {
        return $builder->whereHas('airlines', function (Builder $airlineQuery) use ($airline, $destinationStrings) {
            $airlineQuery->where('airlines.id', $airline->id)
                ->whereIn('destination', $destinationStrings);
        });
    }

    public function wakeCategory(): HasOne
    {
        return $this->hasOne(WakeCategory::class);
    }
}
