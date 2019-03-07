<?php

namespace App\Models;

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
}
