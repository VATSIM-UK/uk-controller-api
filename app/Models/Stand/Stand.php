<?php

namespace App\Models\Stand;

use App\Models\Airfield\Airfield;
use App\Models\Vatsim\NetworkAircraft;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
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

    public function airfield(): BelongsTo
    {
        return $this->belongsTo(Airfield::class);
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
}
