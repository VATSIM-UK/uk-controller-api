<?php
namespace App\Models\AltimeterSettingRegions;

use Illuminate\Database\Eloquent\Model;

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
        'station',
        'variation',
    ];
}
