<?php

namespace App\Models\Runway;

use App\Models\Airfield\Airfield;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Runway extends Model
{
    protected $fillable = [
        'airfield_id',
        'identifier',
        'threshold_latitude',
        'threshold_longitude',
        'heading',
    ];

    protected $casts = [
        'airfield_id' => 'integer',
        'threshold_latitude' => 'double',
        'threshold_longitude' => 'double',
        'heading' => 'integer',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
    ];

    public function airfield(): BelongsTo
    {
        return $this->belongsTo(Airfield::class);
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
}
