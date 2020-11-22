<?php

namespace App\Models\Airfield;

use App\Models\Airline\Airline;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Terminal extends Model
{
    protected $fillable = [
        'airfield_id',
        'key',
        'description',
    ];

    public function airlines(): BelongsToMany
    {
        return $this->belongsToMany(Airline::class);
    }
}
