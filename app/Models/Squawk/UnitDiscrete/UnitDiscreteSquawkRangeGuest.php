<?php

namespace App\Models\Squawk\UnitDiscrete;

use Illuminate\Database\Eloquent\Model;

class UnitDiscreteSquawkRangeGuest extends Model
{
    protected $dates = [
        'created_at',
        'updated_at'
    ];

    protected $fillable = [
        'primary_unit',
        'guest_unit',
    ];
}
