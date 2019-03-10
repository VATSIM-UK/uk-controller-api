<?php

namespace App\Models;

use App\Models\MinStack\MslTma;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Tma extends Model
{
    public $timestamps = true;

    protected $table = 'tma';

    /**
     * @var array
     */
    protected $fillable = [
        'name',
        'description',
        'transition_altitude',
        'standard_high',
        'msl_calculation',
        'created_at',
        'updated_at'
    ];

    /**
     * @return HasOne
     */
    public function mslCalculation() : HasOne
    {
        return $this->hasOne(TmaMslCalculation::class);
    }

    /**
     * @return HasOne
     */
    public function msl() : HasOne
    {
        return $this->hasOne(MslTma::class);
    }
}
