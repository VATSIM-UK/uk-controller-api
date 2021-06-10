<?php

namespace App\Models\Controller;

use App\Models\Airfield\Airfield;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class ControllerPosition extends Model
{
    use HasFactory;

    public $timestamps = true;

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    protected $fillable = [
        'callsign',
        'frequency',
        'requests_departure_releases',
        'receives_departure_releases',
        'created_at',
    ];

    protected $casts = [
        'frequency' => 'float',
        'requests_departure_releases' => 'boolean',
        'receives_departure_releases' => 'boolean',
    ];

    public function topDownAirfields() : BelongsToMany
    {
        return $this->belongsToMany(
            Airfield::class,
            'top_downs',
            'controller_position_id',
            'airfield_id'
        );
    }

    public function scopeCanRequestDepartureReleases(Builder $query): Builder
    {
        return $query->where('requests_departure_releases', true);
    }

    public function scopeCanReceiveDepartureReleases(Builder $query): Builder
    {
        return $query->where('receives_departure_releases', true);
    }
}
