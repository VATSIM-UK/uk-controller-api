<?php

namespace App\Models\AltimeterSettingRegions;

use App\Models\Airfield\Airfield;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * Model for an Altimeter Setting Region and the airfields used to determine its pressure.
 *
 * Class AltimeterSettingRegion
 * @package App\Models\AltimeterSettingRegions
 */
class AltimeterSettingRegion extends Model
{
    protected $table = 'altimeter_setting_region';

    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'key',
        'station',
        'variation',
    ];

    public function airfields() : BelongsToMany
    {
        return $this->belongsToMany(
            Airfield::class,
            'altimeter_setting_region_airfield',
            'altimeter_setting_region_id',
            'airfield_id'
        );
    }
}
