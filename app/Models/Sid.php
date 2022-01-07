<?php

namespace App\Models;

use App\Models\Controller\Handoff;
use App\Models\Controller\Prenote;
use App\Models\Departure\SidDepartureIntervalGroup;
use App\Models\Runway\Runway;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Sid extends Model
{
    public $timestamps = true;

    public $table = 'sid';

    protected $fillable = [
        'airfield_id',
        'runway_id',
        'identifier',
        'initial_altitude',
        'initial_heading',
        'handoff_id',
        'sid_departure_interval_group_id',
        'created_at',
        'updated_at',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    public function runway() : BelongsTo
    {
        return $this->belongsTo(Runway::class);
    }

    public function handoff() : HasOne
    {
        return $this->hasOne(Handoff::class, 'id', 'handoff_id');
    }

    public function prenotes() : BelongsToMany
    {
        return $this->belongsToMany(
            Prenote::class,
            'sid_prenotes',
            'sid_id',
            'prenote_id'
        );
    }

    public function departureIntervalGroup(): BelongsTo
    {
        return $this->belongsTo(SidDepartureIntervalGroup::class);
    }
}
