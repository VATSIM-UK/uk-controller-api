<?php

namespace App\Models\AltimeterSettingRegions;

use App\Models\AltimeterSettingRegions\AltimeterSettingRegion;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class RegionalPressureSetting extends Model
{
    protected $fillable = [
        'altimeter_setting_region_id',
        'value',
    ];

    public function altimeterSettingRegion() : HasOne
    {
        return $this->hasOne(AltimeterSettingRegion::class);
    }
}
