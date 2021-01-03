<?php

namespace App\Models\Aircraft;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class RecatCategory extends Model
{
    protected $fillable = [
        'code',
        'description'
    ];

    public function departureIntervals(): BelongsToMany
    {
        return $this->belongsToMany(
            RecatCategory::class,
            'departure_recat_wake_intervals',
            'lead_recat_category_id',
            'following_recat_category_id'
        )->withPivot('interval');
    }
}
