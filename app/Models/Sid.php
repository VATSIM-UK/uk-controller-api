<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Sid extends Model
{
    public $timestamps = true;

    public $table = 'sid';

    protected $fillable = [
        'airfield_id',
        'identifier',
        'initial_altitude'
    ];

    /**
     * @return HasOne
     */
    public function airfield() : HasOne
    {
        return $this->hasOne(Airfield::class);
    }
}
