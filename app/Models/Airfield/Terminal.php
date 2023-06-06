<?php

namespace App\Models\Airfield;

use App\Models\Stand\Stand;
use App\Models\Airline\Airline;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Terminal extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'airfield_id',
        'key',
        'description',
    ];

    public function airfield(): BelongsTo
    {
        return $this->belongsTo(Airfield::class);
    }

    public function airlines(): BelongsToMany
    {
        return $this->belongsToMany(Airline::class)
            ->withPivot([
                'id',
                'priority',
                'destination',
                'full_callsign',
                'callsign_slug',
                'aircraft_id',
            ])
            ->withTimestamps();
    }

    public function stands(): HasMany
    {
        return $this->hasMany(Stand::class);
    }

    public function getAirfieldDescriptionAttribute(): string
    {
        return sprintf('%s / %s', $this->airfield->code, $this->description);
    }
}
