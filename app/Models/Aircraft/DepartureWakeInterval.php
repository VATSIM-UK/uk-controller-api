<?php

namespace App\Models\Aircraft;

use App\Models\Measurement\MeasurementUnit;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

class DepartureWakeInterval extends Pivot
{
    public $incrementing = true;

    public function measurementUnit(): BelongsTo
    {
        return $this->belongsTo(MeasurementUnit::class);
    }
}
