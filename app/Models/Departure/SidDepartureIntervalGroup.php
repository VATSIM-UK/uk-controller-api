<?php

namespace App\Models\Departure;

use App\Models\Sid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SidDepartureIntervalGroup extends Model
{
    protected $fillable = [
        'key',
        'description',
    ];

    public function sids(): HasMany
    {
        return $this->hasMany(Sid::class);
    }

    public function relatedGroups(): BelongsToMany
    {
        return $this->belongsToMany(
            SidDepartureIntervalGroup::class,
            'sid_departure_interval_group_sid_departure_interval_group',
            'lead_group_id',
            'follow_group_id'
        )->withPivot('interval');
    }
}
