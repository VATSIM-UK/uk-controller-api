<?php

namespace App\Models\AltimeterSettingRegions;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RegionalPressureSetting extends Model
{
    protected $fillable = [
        'altimeter_setting_region_id',
        'value',
    ];

    public function altimeterSettingRegion() : BelongsTo
    {
        return $this->belongsTo(AltimeterSettingRegion::class);
    }
}
