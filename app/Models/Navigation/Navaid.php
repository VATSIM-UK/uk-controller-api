<?php

namespace App\Models\Navigation;

use App\Models\Hold\AssignedHold;
use App\Models\Hold\Hold;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Navaid extends Model
{
    protected $fillable = [
        'identifier',
        'latitude',
        'longitude',
    ];

    public function holds(): HasMany
    {
        return $this->hasMany(Hold::class);
    }

    public function assignedHolds(): HasMany
    {
        return $this->hasMany(AssignedHold::class);
    }
}
