<?php

namespace App\Models\Airline;

use App\Models\Stand\Stand;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Airline extends Model
{
    protected $fillable = [
        'icao_code',
        'name',
        'callsign'
    ];

    public function stands(): BelongsToMany
    {
        return $this->belongsToMany(
            Stand::class,
            'airline_stand',
            'airline_id',
            'stand_id'
        )->withTimestamps();
    }
}
