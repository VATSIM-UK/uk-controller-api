<?php

namespace App\Models\Metars;

use App\Exceptions\MetarException;
use App\Models\Airfield\Airfield;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Metar extends Model
{
    protected $fillable = [
        'airfield_id',
        'qnh',
        'raw',
    ];

    protected $casts = [
        'airfield_id' => 'integer',
        'qnh' => 'integer',
    ];

    public function airfield(): BelongsTo
    {
        return $this->belongsTo(Airfield::class);
    }
}
