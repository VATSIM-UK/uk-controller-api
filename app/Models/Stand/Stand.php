<?php

namespace App\Models\Stand;

use App\Models\Airfield\Airfield;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Stand extends Model
{
    protected $fillable = [
        'airfield_id',
        'identifier',
        'latitude',
        'longitude',
    ];

    public function airfield(): BelongsTo
    {
        return $this->belongsTo(Airfield::class);
    }
}
