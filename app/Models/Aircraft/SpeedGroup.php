<?php

namespace App\Models\Aircraft;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class SpeedGroup extends Model
{
    protected $fillable = [
        'airfield_id',
        'key',
    ];

    public function aircraft(): BelongsToMany
    {
        return $this->belongsToMany(Aircraft::class);
    }

    public function engineTypes(): BelongsToMany
    {
        return $this->belongsToMany(EngineType::class);
    }

    public function relatedGroups(): BelongsToMany
    {
        return $this->belongsToMany(
            SpeedGroup::class,
            'speed_group_speed_group',
            'lead_speed_group_id',
            'follow_speed_group_id'
        )->withPivot('penalty');
    }
}
