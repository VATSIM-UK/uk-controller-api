<?php

namespace App\Models\Airfield;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class VisualReferencePoint extends Model
{
    protected $fillable = [
        'name',
        'latitude',
        'longitude',
    ];

    public function airfields(): BelongsToMany
    {
        return $this->belongsToMany(Airfield::class);
    }
}
