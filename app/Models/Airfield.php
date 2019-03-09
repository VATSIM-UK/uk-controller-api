<?php

namespace App\Models;

use App\Models\MinStack\MslAirfield;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Airfield extends Model
{
    public $timestamps = true;

    protected $table = 'airfield';

    /**
     * @var array
     */
    protected $fillable = [
        'code',
        'transition_altitude',
        'created_at',
        'updated_at'
    ];

    /**
     * @return HasOne
     */
    public function mslCalculation() : HasOne
    {
        return $this->hasOne(AirfieldMslCalculation::class);
    }

    /**
     * @return HasOne
     */
    public function msl() : HasOne
    {
        return $this->hasOne(MslAirfield::class);
    }
}
