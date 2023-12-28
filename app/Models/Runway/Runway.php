<?php

namespace App\Models\Runway;

use App\Models\Airfield\Airfield;
use App\Models\Sid;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Runway extends Model
{
    protected $fillable = [
        'airfield_id',
        'identifier',
        'threshold_latitude',
        'threshold_longitude',
        'heading',
        'glideslope_angle',
        'threshold_elevation',
    ];

    protected $casts = [
        'airfield_id' => 'integer',
        'threshold_latitude' => 'double',
        'threshold_longitude' => 'double',
        'heading' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'glideslope_angle' => 'double',
        'threshold_elevation' => 'integer',
    ];

    public function airfield(): BelongsTo
    {
        return $this->belongsTo(Airfield::class);
    }

    public function sids(): HasMany
    {
        return $this->hasMany(Sid::class);
    }

    public function inverses(): BelongsToMany
    {
        return $this->belongsToMany(
            Runway::class,
            'runway_runway',
            'first_runway_id',
            'second_runway_id',
        )->withTimestamps();
    }

    public function scopeAtAirfield(Builder $builder, string $airfield): Builder
    {
        return $builder->whereHas('airfield', function (Builder $airfieldQuery) use ($airfield) {
            $airfieldQuery->where('code', $airfield);
        });
    }
}
